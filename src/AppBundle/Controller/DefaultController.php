<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Link;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
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
            'success' => true,
            'result' => null,
            'errors' => null
        ];

        $form = $this->createForm('AppBundle\Form\LinkType', new Link(), ['csrf_protection' => false,]);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            $response['success'] = false;
            foreach ($form->getErrors(true) as $error) {
                $response['errors'][] = $error->getMessage();
            }

            return new JsonResponse($response);
        }

        $em = $this->getDoctrine()->getManager();
        $link = $form->getData();

        try {
            if (!$generatedCode = $this->get('app.link_generator')->generateCode()) {
                throw new \Exception('Code has not been generated. Try again');
            }

            $link->setCode($generatedCode);
            $em->persist($link);
            $em->flush();

            $response['result']['url'] = $this->generateUrl('redirect', ['code' => $link->getCode()], true);

        } catch (UniqueConstraintViolationException $e) {
            $response['success'] = false;
            $response['errors'] = ['Duplicate code. Try Again'];
        } catch (\Exception $e) {
            $response['success'] = false;
            $response['errors'] = [$e->getMessage()];
        }

        return new JsonResponse($response);
    }

}
