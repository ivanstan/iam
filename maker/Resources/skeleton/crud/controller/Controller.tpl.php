<?= "<?php\n" ?>

namespace <?= $namespace ?>;

use <?= $entity_full_class_name ?>;
use <?= $form_full_class_name ?>;
<?php if (isset($repository_full_class_name)): ?>
use <?= $repository_full_class_name ?>;
<?php endif ?>
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\<?= $parent_class_name ?>;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

<?php if ($use_attributes) { ?>
#[Route('<?= $route_path ?>')]
<?php } else { ?>
/**
 * @Route("<?= $route_path ?>")
 */
<?php } ?>
class <?= $class_name ?> extends <?= $parent_class_name; ?><?= "\n" ?>
{
<?php if ($use_attributes) { ?>
    #[Route('/', name: '<?= $route_name ?>_index', methods: ['GET'])]
<?php } else { ?>
    /**
     * @Route("/", name="<?= $route_name ?>_index", methods={"GET"})
     */
<?php } ?>
<?php if (isset($repository_full_class_name)): ?>
    public function index(Request $request, <?= $repository_class_name ?> $repository): Response
    {
        $<?= $entity_var_plural ?> = $repository->findAll();

        $pager = new Pagerfanta(new ArrayAdapter($<?= $entity_var_plural ?>));
        $pager->setCurrentPage($request->query->get('page', 1));

        return $this->render('<?= $templates_path ?>/index.html.twig', [
            'pager' => $pager,
        ]);
    }
<?php else: ?>
    public function index(Request $request): Response
    {
        $<?= $entity_var_plural ?> = $this->getDoctrine()
            ->getRepository(<?= $entity_class_name ?>::class)
            ->findAll();

        $pager = new Pagerfanta(new ArrayAdapter($<?= $entity_var_plural ?>));
        $pager->setCurrentPage($request->query->get('page', 1));

        return $this->render('<?= $templates_path ?>/index.html.twig', [
            'pager' => $pager,
        ]);
    }
<?php endif ?>

<?php if ($use_attributes) { ?>
    #[Route('/{<?= $entity_identifier ?>}', name: '<?= $route_name ?>_show', methods: ['GET'], requirements: ['<?= $entity_identifier ?>' => '\d+'])]
<?php } else { ?>
    /**
     * @Route("/{<?= $entity_identifier ?>}", name="<?= $route_name ?>_show", methods={"GET"}, requirements={"<?= $entity_identifier ?>"="\d+"})
     */
<?php } ?>
    public function show(<?= $entity_class_name ?> $<?= $entity_var_singular ?>): Response
    {
        return $this->render('<?= $templates_path ?>/show.html.twig', [
            '<?= $entity_twig_var_singular ?>' => $<?= $entity_var_singular ?>,
        ]);
    }

<?php if ($use_attributes) { ?>
    #[Route('/new', name: '<?= $route_name ?>_new', methods: ['GET', 'POST'])]
    #[Route('/{<?= $entity_identifier ?>}/edit', name: '<?= $route_name ?>_edit', methods: ['GET', 'POST'], requirements: ['<?= $entity_identifier ?>' => '\d+'])]
<?php } else { ?>
    /**
     * @Route("/new", name="<?= $route_name ?>_new", methods={"GET","POST"})
     * @Route("/{<?= $entity_identifier ?>}/edit", name="<?= $route_name ?>_edit", methods={"GET","POST"}, requirements={"<?= $entity_identifier ?>"="\d+"})
     */
<?php } ?>
    public function edit(Request $request, ?<?= $entity_class_name ?> $<?= $entity_var_singular ?>): Response
    {
        if ($<?= $entity_var_singular ?> === null) {
            $<?= $entity_var_singular ?> = new <?= $entity_class_name ?>();
        }

        $form = $this->createForm(<?= $form_class_name ?>::class, $<?= $entity_var_singular ?>);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($<?= $entity_var_singular ?>->getId() === null) {
                $this->getDoctrine()->getManager()->persist($<?= $entity_var_singular ?>);
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('<?= $route_name ?>_index');
        }

        return $this->render('<?= $templates_path ?>/edit.html.twig', [
            '<?= $entity_twig_var_singular ?>' => $<?= $entity_var_singular ?>,
            'form' => $form->createView(),
        ]);
    }

<?php if ($use_attributes) { ?>
    #[Route('/{<?= $entity_identifier ?>}', name: '<?= $route_name ?>_delete', methods: ['DELETE'], requirements: ['<?= $entity_identifier ?>' => '\d+'])]
<?php } else { ?>
    /**
     * @Route("/{<?= $entity_identifier ?>}", name="<?= $route_name ?>_delete", methods={"DELETE"}, requirements={"<?= $entity_identifier ?>"="\d+"})
     */
<?php } ?>
    public function delete(Request $request, <?= $entity_class_name ?> $<?= $entity_var_singular ?>): Response
    {
        if ($this->isCsrfTokenValid('delete'.$<?= $entity_var_singular ?>->get<?= ucfirst($entity_identifier) ?>(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($<?= $entity_var_singular ?>);
            $entityManager->flush();
        }

        return $this->redirectToRoute('<?= $route_name ?>_index');
    }
}
