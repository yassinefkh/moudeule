<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Cours;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Contrôleur gérant les documents associés aux cours
 *
 * Ce contrôleur permet le téléchargement, l'affichage et la suppression
 * des documents associés aux différents cours de la plateforme.
 *
 * @package App\Http\Controllers
 */
class DocumentController extends Controller
{
    /**
     * Enregistre un nouveau document associé à un cours
     *
     * Cette méthode vérifie l'existence du cours, valide le fichier téléchargé
     * selon des critères spécifiques, puis stocke le document dans le système.
     *
     * @param  \Illuminate\Http\Request  $request  La requête HTTP contenant les données du document
     * @param  int  $coursId                       L'identifiant du cours
     * @return \Illuminate\Http\RedirectResponse   Redirection avec message de succès ou d'erreur
     */
    public function store(Request $request, $coursId): RedirectResponse
    {
        try {
            // Vérifier d'abord si le cours existe
            $cours = Cours::findOrFail($coursId);

            // Validation personnalisée avec messages d'erreur clairs
            $validator = \Validator::make($request->all(), [
                'titre' => 'required|string|max:255',
                'fichier' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,rar,jpg,jpeg,png,gif|max:400200',
                'section_id' => 'nullable|exists:sections,id',
            ], [
                'titre.required' => 'Le titre du document est obligatoire.',
                'titre.max' => 'Le titre ne peut pas dépasser 255 caractères.',
                'fichier.required' => 'Veuillez sélectionner un fichier à télécharger.',
                'fichier.file' => 'Le document doit être un fichier valide.',
                'fichier.mimes' => 'Format de fichier non autorisé. Formats acceptés : PDF, Word, PowerPoint, Excel, archives (ZIP/RAR) et images.',
                'fichier.max' => 'Le fichier est trop volumineux. La taille maximale est de 400 Mo.',
                'section_id.exists' => 'La section sélectionnée n\'existe pas.',
            ]);

            if ($validator->fails()) {
                return back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Gérer explicitement le téléchargement du fichier
            try {
                $path = $request->file('fichier')->store('documents', 'public');
            } catch (\Exception $e) {
                Log::error('Erreur lors du téléchargement du fichier: ' . $e->getMessage());
                return back()
                    ->withErrors(['fichier' => 'Une erreur est survenue lors du téléchargement. Le fichier est peut-être trop volumineux.'])
                    ->withInput();
            }

            // Créer le document en base de données
            Document::create([
                'cours_id' => $coursId,
                'section_id' => $request->section_id,
                'titre' => $request->titre,
                'fichier' => $path,
            ]);

            return back()->with('success', 'Document ajouté avec succès.');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'Ce cours n\'existe pas.');
        } catch (\Exception $e) {
            Log::error('Erreur dans DocumentController@store: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de l\'ajout du document. Veuillez réessayer.');
        }
    }

    /**
     * Télécharge un document spécifique
     *
     * Cette méthode vérifie l'existence physique du fichier sur le serveur
     * puis le propose au téléchargement avec son nom d'origine.
     *
     * @param  int  $id  L'identifiant du document à télécharger
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\RedirectResponse  
     *         Flux de téléchargement du document ou redirection avec message d'erreur
     */
    public function download($id)
    {
        try {
            $document = Document::findOrFail($id);

            // Vérifier si le fichier existe physiquement
            if (!Storage::disk('public')->exists($document->fichier)) {
                return back()->with('error', 'Le fichier demandé n\'existe plus sur le serveur.');
            }

            // Obtenir l'extension du fichier pour le téléchargement
            $extension = pathinfo(storage_path('app/public/' . $document->fichier), PATHINFO_EXTENSION);
            $filename = $document->titre . ($extension ? '.' . $extension : '');

            return Storage::disk('public')->download($document->fichier, $filename);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'Le document demandé n\'existe pas.');
        } catch (\Exception $e) {
            Log::error('Erreur dans DocumentController@download: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors du téléchargement du document.');
        }
    }

    /**
     * Affiche la liste des documents pour un cours spécifique
     *
     * @param  int  $coursId  L'identifiant du cours
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse  Vue avec les documents ou redirection
     */
    public function index($coursId)
    {
        try {
            $cours = Cours::with([
                'documents' => function ($query) {
                    $query->orderBy('created_at', 'desc');
                },
                'sections'
            ])->findOrFail($coursId);

            return view('documents.index', compact('cours'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('cours.index')->with('error', 'Le cours demandé n\'existe pas.');
        } catch (\Exception $e) {
            Log::error('Erreur dans DocumentController@index: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de l\'affichage des documents.');
        }
    }

    /**
     * Supprime un document spécifique d'un cours
     *
     * Cette méthode supprime à la fois l'entrée en base de données
     * et le fichier physique du serveur.
     *
     * @param  int  $coursId      L'identifiant du cours
     * @param  int  $documentId   L'identifiant du document à supprimer
     * @return \Illuminate\Http\RedirectResponse  Redirection avec message de succès ou d'erreur
     */
    public function destroy($coursId, $documentId): RedirectResponse
    {
        try {
            // Trouve le document qui appartient au cours spécifié
            $document = Document::where('cours_id', $coursId)->findOrFail($documentId);

            // Supprimer le fichier du disque si présent
            if (Storage::disk('public')->exists($document->fichier)) {
                Storage::disk('public')->delete($document->fichier);
            }

            // Supprimer l'entrée en base de données
            $document->delete();

            return back()->with('success', 'Document supprimé avec succès.');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'Le document demandé n\'existe pas ou n\'appartient pas à ce cours.');
        } catch (\Exception $e) {
            Log::error('Erreur dans DocumentController@destroy: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la suppression du document.');
        }
    }
}