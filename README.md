## About this repo

This is a minimal reproduction case of the post-submit form validation issue with Doctrine configured with a cache pool (Memcached or Redis).

![screenshot](https://raw.githubusercontent.com/DevManen/test-form-validation/main/profiler_cache.png "Screenshot of the Web Profiler")

## Steps of reproduction

1) ```composer create-project symfony/skeleton test-form-validation "5.4.*"```
2) ```cd test-form-validation```
3) ```composer require symfony/orm-pack symfony/form symfony/validator```
4) ```composer require --dev symfony/profiler-pack symfony/stopwatch symfony/debug-bundle```
5) Configure the cache and Doctrine 
6) Make an entity
7) Make a controller with an action using _Symfony\Component\Form\Form->handleRequest()_ or _Symfony\Component\Form\Form->submit()_
8) ```php bin/console doctrine:database:create```
9) ```php bin/console doctrine:schema:update --force```
10) Start the local server with ```symfony server:start```
11) Go to [http://127.0.0.1:8000]() and submit the form
12) Check the **Cache** section of the [**Web Profiler**](http://127.0.0.1:8000/_profiler)

## Stack trace from a cache `_MemcachedAdapter->fetch()_` call at form submittion

```
#0 vendor\symfony\cache\Traits\AbstractAdapterTrait.php(224): Symfony\Component\Cache\Adapter\MemcachedAdapter->doFetch(Array)
#1 vendor\symfony\cache\Adapter\TraceableAdapter.php(77): Symfony\Component\Cache\Adapter\AbstractAdapter->getItem('Symfony__Compon...')
#2 vendor\doctrine\persistence\src\Persistence\Mapping\AbstractClassMetadataFactory.php(253): Symfony\Component\Cache\Adapter\TraceableAdapter->getItem('Symfony__Compon...')
#3 vendor\doctrine\orm\lib\Doctrine\ORM\EntityManager.php(313): Doctrine\Persistence\Mapping\AbstractClassMetadataFactory->getMetadataFor('Symfony\\Compone...')
#4 var\cache\dev\ContainerMvr70yM\EntityManager_9a5be93.php(94): Doctrine\ORM\EntityManager->getClassMetadata('Symfony\\Compone...')
#5 vendor\symfony\doctrine-bridge\PropertyInfo\DoctrineExtractor.php(214): ContainerMvr70yM\EntityManager_9a5be93->getClassMetadata('Symfony\\Compone...')
#6 vendor\symfony\doctrine-bridge\PropertyInfo\DoctrineExtractor.php(201): Symfony\Bridge\Doctrine\PropertyInfo\DoctrineExtractor->getMetadata('Symfony\\Compone...')
#7 vendor\symfony\property-info\PropertyInfoExtractor.php(112): Symfony\Bridge\Doctrine\PropertyInfo\DoctrineExtractor->isWritable('Symfony\\Compone...', 'propertyPath', Array)
#8 vendor\symfony\property-info\PropertyInfoExtractor.php(90): Symfony\Component\PropertyInfo\PropertyInfoExtractor->extract(Object(Symfony\Component\DependencyInjection\Argument\RewindableGenerator), 'isWritable', Array)
#9 vendor\symfony\validator\Mapping\Loader\PropertyInfoLoader.php(60): Symfony\Component\PropertyInfo\PropertyInfoExtractor->isWritable('Symfony\\Compone...', 'propertyPath')
#10 vendor\symfony\validator\Mapping\Loader\LoaderChain.php(54): Symfony\Component\Validator\Mapping\Loader\PropertyInfoLoader->loadClassMetadata(Object(Symfony\Component\Validator\Mapping\ClassMetadata))
#11 vendor\symfony\validator\Mapping\Factory\LazyLoadingMetadataFactory.php(101): Symfony\Component\Validator\Mapping\Loader\LoaderChain->loadClassMetadata(Object(Symfony\Component\Validator\Mapping\ClassMetadata))
#12 vendor\symfony\validator\Mapping\Factory\LazyLoadingMetadataFactory.php(135): Symfony\Component\Validator\Mapping\Factory\LazyLoadingMetadataFactory->getMetadataFor('Symfony\\Compone...')
#13 vendor\symfony\validator\Mapping\Factory\LazyLoadingMetadataFactory.php(109): Symfony\Component\Validator\Mapping\Factory\LazyLoadingMetadataFactory->mergeConstraints(Object(Symfony\Component\Validator\Mapping\ClassMetadata))
#14 vendor\symfony\validator\Validator\RecursiveContextualValidator.php(306): Symfony\Component\Validator\Mapping\Factory\LazyLoadingMetadataFactory->getMetadataFor(Object(Symfony\Component\Form\Form))
#15 vendor\symfony\validator\Validator\RecursiveContextualValidator.php(138): Symfony\Component\Validator\Validator\RecursiveContextualValidator->validateObject(Object(Symfony\Component\Form\Form), '', Array, 1, Object(Symfony\Component\Validator\Context\ExecutionContext))
#16 vendor\symfony\validator\Validator\RecursiveValidator.php(93): Symfony\Component\Validator\Validator\RecursiveContextualValidator->validate(Object(Symfony\Component\Form\Form), NULL, Array)
#17 vendor\symfony\validator\Validator\TraceableValidator.php(66): Symfony\Component\Validator\Validator\RecursiveValidator->validate(Object(Symfony\Component\Form\Form), NULL, NULL)
#18 vendor\symfony\form\Extension\Validator\EventListener\ValidationListener.php(50): Symfony\Component\Validator\Validator\TraceableValidator->validate(Object(Symfony\Component\Form\Form))
#19 vendor\symfony\event-dispatcher\EventDispatcher.php(230): Symfony\Component\Form\Extension\Validator\EventListener\ValidationListener->validateForm(Object(Symfony\Component\Form\Event\PostSubmitEvent), 'form.post_submi...', Object(Symfony\Component\EventDispatcher\EventDispatcher))
#20 vendor\symfony\event-dispatcher\EventDispatcher.php(59): Symfony\Component\EventDispatcher\EventDispatcher->callListeners(Array, 'form.post_submi...', Object(Symfony\Component\Form\Event\PostSubmitEvent))
#21 vendor\symfony\event-dispatcher\ImmutableEventDispatcher.php(33): Symfony\Component\EventDispatcher\EventDispatcher->dispatch(Object(Symfony\Component\Form\Event\PostSubmitEvent), 'form.post_submi...')
#22 vendor\symfony\form\Form.php(681): Symfony\Component\EventDispatcher\ImmutableEventDispatcher->dispatch(Object(Symfony\Component\Form\Event\PostSubmitEvent), 'form.post_submi...')
#23 vendor\symfony\form\Extension\HttpFoundation\HttpFoundationRequestHandler.php(109): Symfony\Component\Form\Form->submit(Array, true)
#24 vendor\symfony\form\Form.php(503): Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationRequestHandler->handleRequest(Object(Symfony\Component\Form\Form), Object(Symfony\Component\HttpFoundation\Request))
#25 src\Controller\ThingController.php(52): Symfony\Component\Form\Form->handleRequest(Object(Symfony\Component\HttpFoundation\Request))
#26 vendor\symfony\http-kernel\HttpKernel.php(152): App\Controller\ThingController->formValidationAction(Object(Symfony\Component\HttpFoundation\Request), Object(Doctrine\Bundle\DoctrineBundle\Registry))
#27 vendor\symfony\http-kernel\HttpKernel.php(74): Symfony\Component\HttpKernel\HttpKernel->handleRaw(Object(Symfony\Component\HttpFoundation\Request), 1)
#28 vendor\symfony\http-kernel\Kernel.php(202): Symfony\Component\HttpKernel\HttpKernel->handle(Object(Symfony\Component\HttpFoundation\Request), 1, true)
#29 vendor\symfony\runtime\Runner\Symfony\HttpKernelRunner.php(35): Symfony\Component\HttpKernel\Kernel->handle(Object(Symfony\Component\HttpFoundation\Request))
#30 vendor\autoload_runtime.php(35): Symfony\Component\Runtime\Runner\Symfony\HttpKernelRunner->run()
#31 public\index.php(5): require_once('X:\\DEVTOOLS\\pro...')
#32 {main}
```
