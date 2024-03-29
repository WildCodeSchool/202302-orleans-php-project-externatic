<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Skill;
use App\Form\SkillType;
use App\Service\Locator;
use App\Entity\Candidate;
use App\Entity\Formation;
use App\Entity\Experience;
use App\Form\CandidateType;
use App\Form\FormationType;
use App\Form\ExperienceType;
use App\Form\ProfilePicType;
use App\Repository\SkillRepository;
use App\Repository\CandidateRepository;
use App\Repository\FormationRepository;
use App\Repository\ExperienceRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/** @SuppressWarnings(PHPMD.TooManyPublicMethods) */
#[IsGranted('ROLE_CANDIDATE')]
class CandidateController extends AbstractController
{
    #[Route('candidat/edit', name: 'app_candidate_edit_profile', methods: ['GET', 'POST'])]
    public function edit(Request $request, CandidateRepository $candidateRepository, Locator $locator): Response
    {
        // Create the form, linked with $candidate
        /** @var User */
        $user = $this->getUser();
        $candidate = $user->getCandidate();
        $form = $this->createForm(CandidateType::class, $candidate);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($candidate->getLocalization()) {
                [$longitude, $latitude] = $locator->getCoordinates($candidate);
                $candidate->setLongitude($longitude)->setLatitude($latitude);
            }
            $candidateRepository->save($candidate, true);
            return $this->redirectToRoute('app_candidate_profile', ['id' => $candidate->getId()]);
        }
        // Render the form

        return $this->render('candidate/edit.html.twig', [
            'candidate' => $candidate, 'form' => $form,
        ]);
    }

    #[Route('candidat/{id}/photo', name: 'app_candidate_edit_profilePic', methods: ['GET', 'POST'])]
    public function editProfilePic(
        Request $request,
        Candidate $candidate,
        CandidateRepository $candidateRepository
    ): Response {
        $form = $this->createForm(ProfilePicType::class, $candidate);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $candidateRepository->save($candidate, true);
            return $this->redirectToRoute('app_candidate_profile', ['id' => $candidate->getId()]);
        }
        return $this->render('candidate/edit_profilePic.html.twig', [
            'candidate' => $candidate, 'form' => $form,
        ]);
    }

    #[Route('/candidat/{id}/competence/supprimer', name: 'app_candidate_delete_skill', methods: ['POST'])]
    public function deleteSkill(
        Request $request,
        Skill $skill,
        SkillRepository $skillRepository
    ): Response {
        /** @var User */
        $user = $this->getUser();
        $candidate = $user->getCandidate();
        if (
            $this->isCsrfTokenValid(
                'delete' . $skill->getId(),
                $request->request->get('_token')
            )
        ) {
            $skillRepository->remove($skill, true);
        }

        return $this->redirectToRoute(
            'app_candidate_profile',
            ['candidate' => $candidate, 'id' => $candidate->getId()],
            Response::HTTP_SEE_OTHER
        );
    }

    #[Route('/{id}/skill/edit', name: 'app_candidate_edit_skill', methods: ['GET', 'POST'])]
    public function editSkill(Request $request, Skill $skill, SkillRepository $skillRepository): Response
    {

        /** @var User */
        $user = $this->getUser();
        $candidate = $user->getCandidate();
        $form = $this->createForm(SkillType::class, $skill);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $skillRepository->save($skill, true);

            return $this->redirectToRoute('app_candidate_profile', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('candidate/edit.html.twig', [
            'skill' => $skill,
            'form' => $form,
            'candidate' => $candidate,
        ]);
    }

    #[Route('/candidat/{id}/formation/supprimer', name: 'app_candidate_delete_formation', methods: ['POST'])]
    public function deleteFormation(
        Request $request,
        Formation $formation,
        FormationRepository $formationRepository
    ): Response {
        /** @var User */
        $user = $this->getUser();
        $candidate = $user->getCandidate();
        if (
            $this->isCsrfTokenValid(
                'delete' . $formation->getId(),
                $request->request->get('_token')
            )
        ) {
            $formationRepository->remove($formation, true);
        }

        return $this->redirectToRoute(
            'app_candidate_profile',
            ['candidate' => $candidate, 'id' => $candidate->getId()],
            Response::HTTP_SEE_OTHER
        );
    }

    #[Route('/{id}/formation/edit', name: 'app_candidate_edit_formation', methods: ['GET', 'POST'])]
    public function editformation(
        Request $request,
        Formation $formation,
        FormationRepository $formationRepository
    ): Response {

        /** @var User */
        $user = $this->getUser();
        $candidate = $user->getCandidate();
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formationRepository->save($formation, true);

            return $this->redirectToRoute('app_candidate_profile', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('candidate/edit.html.twig', [
            'formation' => $formation,
            'form' => $form,
            'candidate' => $candidate,
        ]);
    }

    #[Route('/candidat/{id}/experience/supprimer', name: 'app_candidate_delete_experience', methods: ['POST'])]
    public function deleteExperience(
        Request $request,
        Experience $experience,
        ExperienceRepository $experienceRepository
    ): Response {
        /** @var User */
        $user = $this->getUser();
        $candidate = $user->getCandidate();
        if (
            $this->isCsrfTokenValid(
                'delete' . $experience->getId(),
                $request->request->get('_token')
            )
        ) {
            $experienceRepository->remove($experience, true);
        }

        return $this->redirectToRoute(
            'app_candidate_profile',
            ['candidate' => $candidate, 'id' => $candidate->getId()],
            Response::HTTP_SEE_OTHER
        );
    }

    #[Route('/{id}/experience/edit', name: 'app_candidate_edit_experience', methods: ['GET', 'POST'])]
    public function editexperience(
        Request $request,
        Experience $experience,
        ExperienceRepository $experienceRepository
    ): Response {

        /** @var User */
        $user = $this->getUser();
        $candidate = $user->getCandidate();
        $form = $this->createForm(ExperienceType::class, $experience);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $experienceRepository->save($experience, true);

            return $this->redirectToRoute('app_candidate_profile', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('candidate/edit.html.twig', [
            'experience' => $experience,
            'form' => $form,
            'candidate' => $candidate,
        ]);
    }

    #[Route('candidat', name: 'app_candidate_profile', methods: ['GET', 'POST'])]
    public function show(): Response
    {
        return $this->render('candidate/profile.html.twig');
    }

    #[Route('candidat/{id}/formation/ajouter', name: 'app_candidate_add_formation')]
    public function newFormation(
        Request $request,
        FormationRepository $formationRepository,
        Candidate $candidate
    ): Response {
        $formation = new Formation();
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $candidate->addFormation($formation);
            $formationRepository->save($formation, true);
            $candidate->addFormation($formation);
            return $this->redirectToRoute(
                'app_candidate_profile',
                ['candidate' => $candidate, 'id' => $candidate->getId()]
            );
        }
        return $this->render('formation/edit.html.twig', [
            'form' => $form,
            'candidate' => $candidate,
        ]);
    }

    #[Route('candidat/{id}/experience/ajouter', name: 'app_candidate_add_experience')]
    public function newExperience(
        Request $request,
        ExperienceRepository $experienceRepository,
        Candidate $candidate
    ): Response {
        $experience = new Experience();
        $form = $this->createForm(ExperienceType::class, $experience);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $candidate->addexperience($experience);
            $experienceRepository->save($experience, true);
            return $this->redirectToRoute(
                'app_candidate_profile',
                ['candidate' => $candidate, 'id' => $candidate->getId()]
            );
        }
        return $this->render('experience/add.html.twig', [
            'form' => $form,
            'candidate' => $candidate,
        ]);
    }

    #[Route('candidat/{id}/competence/ajouter', name: 'app_candidate_add_skill')]
    public function newSkill(
        Request $request,
        SkillRepository $skillRepository,
        Candidate $candidate
    ): Response {
        $skill = new Skill();
        $form = $this->createForm(SkillType::class, $skill);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $candidate->addskill($skill);
            $skillRepository->save($skill, true);
            return $this->redirectToRoute(
                'app_candidate_profile',
                ['candidate' => $candidate, 'id' => $candidate->getId()]
            );
        }
        return $this->render('skill/add.html.twig', [
            'form' => $form,
            'candidate' => $candidate,
        ]);
    }

    #[Route('/profil-candidat', name: 'app_candidate_show', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('candidate/show.html.twig');
    }
}
