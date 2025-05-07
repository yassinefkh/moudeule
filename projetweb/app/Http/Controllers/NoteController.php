<?php

namespace App\Http\Controllers;

use App\Models\Controle;
use App\Models\Cours;
use App\Models\Note;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Contrôleur gérant les notes des étudiants
 *
 * Ce contrôleur permet la création, la modification et l'affichage des notes
 * associées aux contrôles pour les étudiants.
 *
 * @package App\Http\Controllers
 */
class NoteController extends Controller
{
    /**
     * Affiche le formulaire pour saisir les notes d'un contrôle
     *
     * @param  int  $coursId     L'identifiant du cours
     * @param  int  $controleId  L'identifiant du contrôle
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse  Vue du formulaire ou redirection
     */
    public function create($coursId, $controleId)
    {
        $course = Cours::findOrFail($coursId);
        $controle = Controle::findOrFail($controleId);
        
        // Vérifier que le contrôle appartient bien au cours
        if ($controle->cours_id != $coursId) {
            return redirect()->back()->with('error', 'Ce contrôle n\'appartient pas à ce cours.');
        }
        
        // Vérifier que l'utilisateur est un enseignant ou un admin
        if (!in_array(Auth::user()->type, ['enseignant', 'admin'])) {
            return redirect()->back()->with('error', 'Vous n\'avez pas la permission de saisir des notes.');
        }
        
        // Récupérer tous les étudiants inscrits au cours
        $students = $course->students;
        
        // Récupérer les notes existantes pour ce contrôle
        $existingNotes = $controle->notes->keyBy('user_id');
        
        return view('notes.create', compact('course', 'controle', 'students', 'existingNotes'));
    }
    
    /**
     * Enregistre les notes d'un contrôle pour plusieurs étudiants
     *
     * @param  \Illuminate\Http\Request  $request     La requête HTTP contenant les notes
     * @param  int  $coursId                          L'identifiant du cours
     * @param  int  $controleId                       L'identifiant du contrôle
     * @return \Illuminate\Http\RedirectResponse      Redirection vers les détails du contrôle
     */
    public function store(Request $request, $coursId, $controleId): RedirectResponse
    {
        $course = Cours::findOrFail($coursId);
        $controle = Controle::findOrFail($controleId);
        
        // Vérifier que le contrôle appartient bien au cours
        if ($controle->cours_id != $coursId) {
            return redirect()->back()->with('error', 'Ce contrôle n\'appartient pas à ce cours.');
        }
        
        // Vérifier que l'utilisateur est un enseignant ou un admin
        if (!in_array(Auth::user()->type, ['enseignant', 'admin'])) {
            return redirect()->back()->with('error', 'Vous n\'avez pas la permission de saisir des notes.');
        }
        
        $validated = $request->validate([
            'notes' => 'required|array',
            'notes.*' => 'nullable|numeric|min:0|max:20',
            'commentaires' => 'nullable|array',
            'commentaires.*' => 'nullable|string'
        ]);
        
        $notes = $validated['notes'];
        $commentaires = $request->input('commentaires', []);
        
        foreach ($notes as $userId => $noteValue) {
            // Vérifier que l'étudiant est bien inscrit au cours
            $student = User::find($userId);
            if (!$student || !$course->students->contains($userId)) {
                continue;
            }
            
            // Si la note est vide, passer à l'étudiant suivant
            if ($noteValue === null || $noteValue === '') {
                continue;
            }
            
            // Créer ou mettre à jour la note
            Note::updateOrCreate(
                [
                    'controle_id' => $controle->id,
                    'user_id' => $userId
                ],
                [
                    'note' => $noteValue,
                    'commentaire' => $commentaires[$userId] ?? null
                ]
            );
        }
        
        return redirect()->route('controles.show', [$course->id, $controle->id])
            ->with('success', 'Notes enregistrées avec succès.');
    }
    
    /**
     * Affiche les notes d'un étudiant pour tous ses cours
     *
     * Cette méthode récupère les cours auxquels l'étudiant est inscrit,
     * les contrôles associés et calcule les moyennes par cours et générale.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse  Vue des notes ou redirection
     */
    public function showStudentNotes()
    {
        // S'assurer que l'utilisateur connecté est un étudiant
        if (Auth::user()->type !== 'etudiant') {
            return redirect()->back()->with('error', 'Cette page est réservée aux étudiants.');
        }
        
        $student = Auth::user();
        $courses = $student->courses;
        
        $courseData = [];
        
        foreach ($courses as $course) {
            $controles = $course->controles;
            $notes = [];
            
            foreach ($controles as $controle) {
                $note = $controle->getNoteForUser($student->id);
                if ($note) {
                    $notes[] = [
                        'controle' => $controle,
                        'note' => $note
                    ];
                }
            }
            
            $moyenne = $course->moyenneForUser($student->id);
            
            $courseData[] = [
                'course' => $course,
                'notes' => $notes,
                'moyenne' => $moyenne
            ];
        }
        
        $moyenneGenerale = $student->moyenneGenerale();
        
        return view('notes.student', compact('student', 'courseData', 'moyenneGenerale'));
    }
}