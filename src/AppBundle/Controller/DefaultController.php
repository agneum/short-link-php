<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Link;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Method("GET")
     */
    public function indexAction()
    {
        return $this->render('default/index.html.twig');
    }

    /**
     * @Route("/link-not-found", name="not-found")
     * @Method("GET")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function notFoundAction()
    {
        return $this->render('default/not-found.html.twig');
    }

    /**
     * @Route("/{code}", name="redirect")
     * @Method("GET")
     *
     * @param $code
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectAction($code)
    {
        $link = $this->getDoctrine()->getRepository('AppBundle:Link')
            ->findOneBy(['code' => $code, 'disabled' => 0]);

        $url = ($link) ? $link->getOriginalLink() : $this->generateUrl('not-found');

        return $this->redirect($url);
    }

    /**
     * @Route("/generate-link", name="generate-link")
     * @Method("POST")
     */
    public function createLinkAction(Request $request)
    {
        $response = [
            'success' => false,
            'result' => null,
            'errors' => null
        ];

        $form = $this->createForm('AppBundle\Form\LinkType', new Link(), ['csrf_protection' => false]);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            foreach ($form->getErrors(true) as $error) {
                $response['errors'][] = $error->getMessage();
            }

            return new JsonResponse($response);
        }

        $link = $this->get('app.link_generator')->completeLink($form->getData());

        if ($link instanceof Link) {
            $response['success'] = true;
            $response['result']['url'] = $this->generateUrl('redirect', ['code' => $link->getCode()], true);
        } else {
            $response['errors'] = ['An error occurred while generating the link, please try again.'];
        }

        return new JsonResponse($response);
    }

}
