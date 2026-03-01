<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\ProductCategory;
use App\Entity\ProductSubcategory;
use App\Form\ProductType;
use App\Repository\ProductCategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\ProductSubcategoryRepository;
use App\Service\BreadcrumbService;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ProductController extends AbstractController
{
    private $breadcrumbService;
    private $translator;
    public function __construct(BreadcrumbService $breadcrumbService, TranslatorInterface $translator)
    {
        $this->breadcrumbService = $breadcrumbService;
        $this->translator = $translator;
    }

    #[Route(
        path: [
            'en' => '/en/product/',
            'fr' => '/fr/produit/',
            'es' => '/es/producto/'
        ], name: 'product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/product/new', name: 'product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/product/{id}', name: 'product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/product/{id}/edit', name: 'product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/product/delete/{id}', name: 'product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('product_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route(
        path: [
            'en' => '/en/product/category/',
            'fr' => '/fr/produit/categorie/',
            'es' => '/es/producto/categoria/'
        ], name: 'category_product_index', methods: ['GET'])]
    public function category_product_index(ProductCategoryRepository $productCategoryRepository): Response
    {
        $page_title = $this->translator->trans('menu.products'). ' - '.$this->translator->trans('menu.products.category');
        $this->breadcrumbService->add($this->translator->trans('menu.dashboard'), $this->generateUrl('home'));
        $this->breadcrumbService->add($this->translator->trans('menu.products'), $this->generateUrl('product_index'));
        $this->breadcrumbService->add($this->translator->trans('menu.products.category'), $this->generateUrl('category_product_index'));
        $breadcrumbs = $this->breadcrumbService->all();
        return $this->render('product/category/index.html.twig', [
            'breadcrumbs' => $breadcrumbs,
            'page_title' => $page_title,
        ]);
    }

    #[Route('/product/category/list', name: 'product_category_list', methods: [ 'POST'])]
    public function product_category_list(ProductCategoryRepository $productCategoryRepository): JsonResponse
    {
        $productsCategories = $productCategoryRepository->findAll();
        //To array
        $productsCategoriesArray = array_map(function ($productCategory) {
            /** @var ProductCategory $productCategory */
            return $productCategory->toArray();
        }, $productsCategories);
        $return = [
            'draw' => 0,
            'recordsTotal' => count($productsCategoriesArray),
            'recordsFiltered' => count($productsCategoriesArray),
            'data' => $productsCategoriesArray
        ];

        return $this->json($return);
    }

    #[Route('/product/category/get', name: 'product_category_get', methods: ['POST'])]
    public function product_category_get(Request $request, ProductCategoryRepository $productCategoryRepository): JsonResponse
    {
        $category_id = $request->request->get('category_id');
        $productCategory = $productCategoryRepository->find($category_id);
        return $this->json($productCategory->toArray());
    }


    #[Route('/product/category/update', name: 'product_category_update', methods: [ 'POST'])]
    public function product_category_update(Request $request, ProductCategoryRepository $productCategoryRepository, EntityManagerInterface $em): JsonResponse
    {
        $category_id = $request->request->get('category_id');
        $image = $request->request->get('image');
        $name_en = $request->request->get('name_en');
        $description_en = $request->request->get('description_en');
        $name_fr = $request->request->get('name_fr');
        $description_fr = $request->request->get('description_fr');
        $name_es = $request->request->get('name_es');
        $description_es = $request->request->get('description_es');
        $enabled = $request->request->get('enabled', false);
        if ($category_id === '0'){
            $productCategory = new ProductCategory();
            $productCategory->setCreatedAt(Carbon::now()->toDateTimeImmutable());
        }else{
            $productCategory = $productCategoryRepository->find($category_id);
            $productCategory->setupdatedAt(Carbon::now()->toDateTimeImmutable());
        }
        $productCategory->translate('en')->setName($name_en);
        $productCategory->translate('fr')->setName($name_fr);
        $productCategory->translate('es')->setName($name_es);
        $productCategory->translate('en')->setDescription($description_en);
        $productCategory->translate('fr')->setDescription($description_fr);
        $productCategory->translate('es')->setDescription($description_es);
        $productCategory->setEnabled($enabled);
        $productCategory->setImage($image);
        $productCategory->mergeNewTranslations();
        $em->persist($productCategory);
        $em->flush();
        return $this->json(['success' => true]);
    }

    #[Route('/product/category/enable', name: 'product_category_enable', methods: [ 'POST'])]
    public function product_category_enable(Request $request, ProductCategoryRepository $productCategoryRepository, EntityManagerInterface $em): JsonResponse
    {

        $category_id = $request->request->get('category_id');
        $productCategory = $productCategoryRepository->find($category_id);
        $productCategory->setupdatedAt(Carbon::now()->toDateTimeImmutable());
        $productCategory->setEnabled(true);
        $em->persist($productCategory);
        $em->flush();
        return $this->json(['success' => true]);
    }
    #[Route('/product/category/disable', name: 'product_category_disable', methods: [ 'POST'])]
    public function product_category_disable(Request $request, ProductCategoryRepository $productCategoryRepository, EntityManagerInterface $em): JsonResponse
    {

        $category_id = $request->request->get('category_id');
        $productCategory = $productCategoryRepository->find($category_id);
        $productCategory->setupdatedAt(Carbon::now()->toDateTimeImmutable());
        $productCategory->setEnabled(false);
        $em->persist($productCategory);
        $em->flush();
        return $this->json(['success' => true]);
    }

    #[Route('/product/category/delete/{id}', name: 'product_category_delete', methods: ['POST'])]
    public function product_category_delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('product_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route( path: [
        'en' => '/en/product/subcategory/',
        'fr' => '/fr/produit/sous-categorie/',
        'es' => '/es/producto/subcategoria/'
    ], name: 'product_subcategory_index', methods: ['GET'])]
    public function product_subcategory_index(Request $request, ProductCategoryRepository $productCategoryRepository): Response
    {
        $locale = $request->getLocale();
        $page_title = $this->translator->trans('menu.products'). ' - '.$this->translator->trans('menu.products.subcategory');
        $this->breadcrumbService->add($this->translator->trans('menu.dashboard'), $this->generateUrl('home'));
        $this->breadcrumbService->add($this->translator->trans('menu.products'), $this->generateUrl('product_index'));
        $this->breadcrumbService->add($this->translator->trans('menu.products.category'), $this->generateUrl('category_product_index'));
        $this->breadcrumbService->add($this->translator->trans('menu.products.subcategory'), $this->generateUrl('product_subcategory_index'));
        $breadcrumbs = $this->breadcrumbService->all();

        $productCategories = $productCategoryRepository->getProductCategories($locale);

        return $this->render('product/subcategory/index.html.twig', [
            'product_categories' => $productCategories,
            'breadcrumbs' => $breadcrumbs,
            'page_title' => $page_title,
        ]);
    }

    #[Route('/product/subcategory/list', name: 'product_subcategory_list', methods: [ 'POST'])]
    public function product_subcategory_list(ProductSubcategoryRepository $productSubcategoryRepository): JsonResponse
    {
        $productsSubcategories = $productSubcategoryRepository->findAll();
        //To array
        $productsSubcategoriesArray = array_map(function ($productSubcategory) {
            /** @var ProductSubcategory $productSubcategory */
            return $productSubcategory->toArray();
        }, $productsSubcategories);
        $return = [
            'draw' => 0,
            'recordsTotal' => count($productsSubcategoriesArray),
            'recordsFiltered' => count($productsSubcategoriesArray),
            'data' => $productsSubcategoriesArray
        ];

        return $this->json($return);
    }

    #[Route('/product/subcategory/get', name: 'product_subcategory_get', methods: ['POST'])]
    public function product_subcategory_get(Request $request, ProductSubcategoryRepository $productSubcategoryRepository): JsonResponse
    {
        $subcategory_id = $request->request->get('subcategory_id');
        $productSubcategory = $productSubcategoryRepository->find($subcategory_id);
        return $this->json($productSubcategory->toArray());
    }

    #[Route('/product/subcategory/update', name: 'product_subcategory_update', methods: [ 'POST'])]
    public function product_subcategory_update(Request $request, ProductSubcategoryRepository $productSubcategoryRepository, ProductCategoryRepository $productCategoryRepository, EntityManagerInterface $em): JsonResponse
    {
        $subcategory_id = $request->request->get('subcategory_id');
        $product_category_id = $request->request->get('product_category_id');
        $productCategory = $productCategoryRepository->find($product_category_id);
        $name_en = $request->request->get('name_en');
        $description_en = $request->request->get('description_en');
        $name_fr = $request->request->get('name_fr');
        $description_fr = $request->request->get('description_fr');
        $name_es = $request->request->get('name_es');
        $description_es = $request->request->get('description_es');
        $enabled = $request->request->get('enabled', false);
        if ($subcategory_id === '0'){
            $productSubcategory = new ProductSubcategory();
            $productSubcategory->setCreatedAt(Carbon::now()->toDateTimeImmutable());
        }else{
            $productSubcategory = $productSubcategoryRepository->find($subcategory_id);
            $productSubcategory->setupdatedAt(Carbon::now()->toDateTimeImmutable());
        }
        $productSubcategory->translate('en')->setName($name_en);
        $productSubcategory->translate('fr')->setName($name_fr);
        $productSubcategory->translate('es')->setName($name_es);
        $productSubcategory->translate('en')->setDescription($description_en);
        $productSubcategory->translate('fr')->setDescription($description_fr);
        $productSubcategory->translate('es')->setDescription($description_es);
        $productSubcategory->setEnabled($enabled);
        $productSubcategory->setProductCategory($productCategory);
        $productSubcategory->mergeNewTranslations();
        $em->persist($productSubcategory);
        $em->flush();
        return $this->json(['success' => true]);
    }

    #[Route('/product/subcategory/enable', name: 'product_subcategory_enable', methods: [ 'POST'])]
    public function product_subcategory_enable(Request $request, ProductSubcategoryRepository $productSubcategoryRepository, EntityManagerInterface $em): JsonResponse
    {

        $subcategory_id = $request->request->get('subcategory_id');
        $productSubcategory = $productSubcategoryRepository->find($subcategory_id);
        $productSubcategory->setupdatedAt(Carbon::now()->toDateTimeImmutable());
        $productSubcategory->setEnabled(true);
        $em->persist($productSubcategory);
        $em->flush();
        return $this->json(['success' => true]);
    }
    #[Route('/product/subcategory/disable', name: 'product_category_disable', methods: [ 'POST'])]
    public function product_subcategory_disable(Request $request, ProductSubcategoryRepository $productSubcategoryRepository, EntityManagerInterface $em): JsonResponse
    {

        $subcategory_id = $request->request->get('subcategory_id');
        $productSubcategory = $productSubcategoryRepository->find($subcategory_id);
        $productSubcategory->setupdatedAt(Carbon::now()->toDateTimeImmutable());
        $productSubcategory->setEnabled(false);
        $em->persist($productSubcategory);
        $em->flush();
        return $this->json(['success' => true]);
    }

    #[Route('/product/{id}', name: 'product_subcategory_delete', methods: ['POST'])]
    public function product_subcategory_delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('product_index', [], Response::HTTP_SEE_OTHER);
    }
}
