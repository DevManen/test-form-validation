<?php

namespace App\Controller;

use App\Entity\Thing;
use App\Form\ThingType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @package App\Controller
 *
 * @Route(
 *     path = "/thing",
 *     name = "api_thing_",
 *     options = {
 *         "expose" = true
 *     }
 * )
 */
class ThingController extends AbstractController
{
    /**
     * @Route(
     *     methods = {"GET", "POST"},
     *     path = "/",
     *     name = "form_validation"
     * )
     *
     * @param Request $request
     * @param ManagerRegistry $doctrine
     *
     * @return Response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function formValidationAction( Request $request, ManagerRegistry $doctrine ): Response
    {
        $form = $this->createForm( ThingType::class );

        if ($request->isMethod(Request::METHOD_POST))
        {
            # Implicit $form->submit() call
            $form->handleRequest( $request );

            if ($form->isSubmitted() && $form->isValid())
            {
                /** @var EntityManager $em */
                $em = $doctrine->getManagerForClass( Thing::class );

                /** @var Thing $thg */
                $thg = $form->getData();

                $em->persist( $thg );
                $em->flush();

                $response = new JsonResponse( $thg->toArray() );
            }
            else
            {
                $response = new JsonResponse( $form->getErrors( true ) );
            }
        }
        else
        {
            $response = new Response('
                <!DOCTYPE html>
                <html lang="en">
                    <head>
                        <meta charset="utf-8">
                        <meta name="robots" content="noindex">
                        <title>Test Form Validation With Cache</title>
                    </head>
                    <body>
                        <form method="post" action="#" enctype="application/x-www-form-urlencoded" target="_blank">
                            <label for="input-name">Name :</label>
                            <input type="text" name="name" id="input-name" placeholder="Type anything" required>
                            <button type="submit">Submit</button>
                        </form>
                    </body>
                </html>
            ');
        }

        return $response;
    }
}
