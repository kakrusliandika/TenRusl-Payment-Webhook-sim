<?php declare(strict_types = 1);

return [
	'lastFullAnalysisTime' => 1764816727,
	'meta' => array (
  'cacheVersion' => 'v12-linesToIgnore',
  'phpstanVersion' => '2.1.32',
  'metaExtensions' => 
  array (
  ),
  'phpVersion' => 80324,
  'projectConfig' => '{conditionalTags: {Larastan\\Larastan\\Rules\\NoEnvCallsOutsideOfConfigRule: {phpstan.rules.rule: %noEnvCallsOutsideOfConfig%}, Larastan\\Larastan\\Rules\\NoModelMakeRule: {phpstan.rules.rule: %noModelMake%}, Larastan\\Larastan\\Rules\\NoUnnecessaryCollectionCallRule: {phpstan.rules.rule: %noUnnecessaryCollectionCall%}, Larastan\\Larastan\\Rules\\NoUnnecessaryEnumerableToArrayCallsRule: {phpstan.rules.rule: %noUnnecessaryEnumerableToArrayCalls%}, Larastan\\Larastan\\Rules\\OctaneCompatibilityRule: {phpstan.rules.rule: %checkOctaneCompatibility%}, Larastan\\Larastan\\Rules\\UnusedViewsRule: {phpstan.rules.rule: %checkUnusedViews%}, Larastan\\Larastan\\Rules\\NoMissingTranslationsRule: {phpstan.rules.rule: %checkMissingTranslations%}, Larastan\\Larastan\\Rules\\ModelAppendsRule: {phpstan.rules.rule: %checkModelAppends%}, Larastan\\Larastan\\Rules\\NoPublicModelScopeAndAccessorRule: {phpstan.rules.rule: %checkModelMethodVisibility%}, Larastan\\Larastan\\Rules\\NoAuthFacadeInRequestScopeRule: {phpstan.rules.rule: %checkAuthCallsWhenInRequestScope%}, Larastan\\Larastan\\Rules\\NoAuthHelperInRequestScopeRule: {phpstan.rules.rule: %checkAuthCallsWhenInRequestScope%}, Larastan\\Larastan\\ReturnTypes\\Helpers\\EnvFunctionDynamicFunctionReturnTypeExtension: {phpstan.broker.dynamicFunctionReturnTypeExtension: %generalizeEnvReturnType%}, Larastan\\Larastan\\ReturnTypes\\Helpers\\ConfigFunctionDynamicFunctionReturnTypeExtension: {phpstan.broker.dynamicFunctionReturnTypeExtension: %checkConfigTypes%}, Larastan\\Larastan\\ReturnTypes\\ConfigRepositoryDynamicMethodReturnTypeExtension: {phpstan.broker.dynamicMethodReturnTypeExtension: %checkConfigTypes%}, Larastan\\Larastan\\ReturnTypes\\ConfigFacadeCollectionDynamicStaticMethodReturnTypeExtension: {phpstan.broker.dynamicStaticMethodReturnTypeExtension: %checkConfigTypes%}, Larastan\\Larastan\\Rules\\ConfigCollectionRule: {phpstan.rules.rule: %checkConfigTypes%}}, parameters: {universalObjectCratesClasses: [Illuminate\\Http\\Request, Illuminate\\Support\\Optional], earlyTerminatingFunctionCalls: [abort, dd], mixinExcludeClasses: [Eloquent], bootstrapFiles: [bootstrap.php], checkOctaneCompatibility: false, noEnvCallsOutsideOfConfig: true, noModelMake: true, noUnnecessaryCollectionCall: true, noUnnecessaryCollectionCallOnly: [], noUnnecessaryCollectionCallExcept: [], noUnnecessaryEnumerableToArrayCalls: false, squashedMigrationsPath: [], databaseMigrationsPath: [], disableMigrationScan: false, disableSchemaScan: false, configDirectories: [], viewDirectories: [], translationDirectories: [], checkModelProperties: false, checkUnusedViews: false, checkMissingTranslations: false, checkModelAppends: true, checkModelMethodVisibility: false, generalizeEnvReturnType: false, checkConfigTypes: false, checkAuthCallsWhenInRequestScope: false, paths: [C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app, C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\routes, C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\config, C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\database, C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests], level: 5, tmpDir: C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\storage\\phpstan, treatPhpDocTypesAsCertain: false, excludePaths: {analyseAndScan: [storage, bootstrap/cache, vendor]}}, rules: [Larastan\\Larastan\\Rules\\UselessConstructs\\NoUselessWithFunctionCallsRule, Larastan\\Larastan\\Rules\\UselessConstructs\\NoUselessValueFunctionCallsRule, Larastan\\Larastan\\Rules\\DeferrableServiceProviderMissingProvidesRule, Larastan\\Larastan\\Rules\\ConsoleCommand\\UndefinedArgumentOrOptionRule], services: {{class: Larastan\\Larastan\\Methods\\RelationForwardsCallsExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\ModelForwardsCallsExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\EloquentBuilderForwardsCallsExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\HigherOrderTapProxyExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\HigherOrderCollectionProxyExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\StorageMethodsClassReflectionExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\Extension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\ModelFactoryMethodsClassReflectionExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\RedirectResponseMethodsClassReflectionExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\MacroMethodsClassReflectionExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\ViewWithMethodsClassReflectionExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Properties\\ModelAccessorExtension, tags: [phpstan.broker.propertiesClassReflectionExtension]}, {class: Larastan\\Larastan\\Properties\\ModelPropertyExtension, tags: [phpstan.broker.propertiesClassReflectionExtension]}, {class: Larastan\\Larastan\\Properties\\HigherOrderCollectionProxyPropertyExtension, tags: [phpstan.broker.propertiesClassReflectionExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\HigherOrderTapProxyExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ContainerArrayAccessDynamicMethodReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension], arguments: {className: Illuminate\\Contracts\\Container\\Container}}, {class: Larastan\\Larastan\\ReturnTypes\\ContainerArrayAccessDynamicMethodReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension], arguments: {className: Illuminate\\Container\\Container}}, {class: Larastan\\Larastan\\ReturnTypes\\ContainerArrayAccessDynamicMethodReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension], arguments: {className: Illuminate\\Foundation\\Application}}, {class: Larastan\\Larastan\\ReturnTypes\\ContainerArrayAccessDynamicMethodReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension], arguments: {className: Illuminate\\Contracts\\Foundation\\Application}}, {class: Larastan\\Larastan\\Properties\\ModelRelationsExtension, tags: [phpstan.broker.propertiesClassReflectionExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ModelOnlyDynamicMethodReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ModelFactoryDynamicStaticMethodReturnTypeExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ModelDynamicStaticMethodReturnTypeExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\AppMakeDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\AuthExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\GuardDynamicStaticMethodReturnTypeExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\AuthManagerExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\DateExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\GuardExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\RequestFileExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\RequestRouteExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\RequestUserExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\EloquentBuilderExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\RelationCollectionExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\TestCaseExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\Support\\CollectionHelper}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\AuthExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\CollectExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\NowAndTodayExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\ResponseExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\ValidatorExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\LiteralExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\CollectionFilterRejectDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\CollectionWhereNotNullDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\NewModelQueryDynamicMethodReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\FactoryDynamicMethodReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\Types\\AbortIfFunctionTypeSpecifyingExtension, tags: [phpstan.typeSpecifier.functionTypeSpecifyingExtension], arguments: {methodName: abort, negate: false}}, {class: Larastan\\Larastan\\Types\\AbortIfFunctionTypeSpecifyingExtension, tags: [phpstan.typeSpecifier.functionTypeSpecifyingExtension], arguments: {methodName: abort, negate: true}}, {class: Larastan\\Larastan\\Types\\AbortIfFunctionTypeSpecifyingExtension, tags: [phpstan.typeSpecifier.functionTypeSpecifyingExtension], arguments: {methodName: throw, negate: false}}, {class: Larastan\\Larastan\\Types\\AbortIfFunctionTypeSpecifyingExtension, tags: [phpstan.typeSpecifier.functionTypeSpecifyingExtension], arguments: {methodName: throw, negate: true}}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\AppExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\ValueExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\StrExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\TapExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\StorageDynamicStaticMethodReturnTypeExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\Types\\GenericEloquentCollectionTypeNodeResolverExtension, tags: [phpstan.phpDoc.typeNodeResolverExtension]}, {class: Larastan\\Larastan\\Types\\ViewStringTypeNodeResolverExtension, tags: [phpstan.phpDoc.typeNodeResolverExtension]}, {class: Larastan\\Larastan\\Rules\\OctaneCompatibilityRule}, {class: Larastan\\Larastan\\Rules\\NoEnvCallsOutsideOfConfigRule, arguments: {configDirectories: %configDirectories%}}, {class: Larastan\\Larastan\\Rules\\NoModelMakeRule}, {class: Larastan\\Larastan\\Rules\\NoUnnecessaryCollectionCallRule, arguments: {onlyMethods: %noUnnecessaryCollectionCallOnly%, excludeMethods: %noUnnecessaryCollectionCallExcept%}}, {class: Larastan\\Larastan\\Rules\\NoUnnecessaryEnumerableToArrayCallsRule}, {class: Larastan\\Larastan\\Rules\\ModelAppendsRule}, {class: Larastan\\Larastan\\Rules\\NoPublicModelScopeAndAccessorRule}, {class: Larastan\\Larastan\\Types\\GenericEloquentBuilderTypeNodeResolverExtension, tags: [phpstan.phpDoc.typeNodeResolverExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\AppEnvironmentReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension], arguments: {class: Illuminate\\Foundation\\Application}}, {class: Larastan\\Larastan\\ReturnTypes\\AppEnvironmentReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension], arguments: {class: Illuminate\\Contracts\\Foundation\\Application}}, {class: Larastan\\Larastan\\ReturnTypes\\AppFacadeEnvironmentReturnTypeExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\Types\\ModelProperty\\ModelPropertyTypeNodeResolverExtension, tags: [phpstan.phpDoc.typeNodeResolverExtension], arguments: {active: %checkModelProperties%}}, {class: Larastan\\Larastan\\Types\\CollectionOf\\CollectionOfTypeNodeResolverExtension, tags: [phpstan.phpDoc.typeNodeResolverExtension]}, {class: Larastan\\Larastan\\Properties\\MigrationHelper, arguments: {databaseMigrationPath: %databaseMigrationsPath%, disableMigrationScan: %disableMigrationScan%, parser: @migrationsParser, reflectionProvider: @reflectionProvider}}, iamcalSqlParser: {class: Larastan\\Larastan\\SQL\\IamcalSqlParser, autowired: false}, sqlParserFactory: {class: Larastan\\Larastan\\SQL\\SqlParserFactory, arguments: {iamcalSqlParser: @iamcalSqlParser}}, sqlParser: {type: Larastan\\Larastan\\SQL\\SqlParser, factory: [@sqlParserFactory, create]}, {class: Larastan\\Larastan\\Properties\\SquashedMigrationHelper, arguments: {schemaPaths: %squashedMigrationsPath%, disableSchemaScan: %disableSchemaScan%}}, {class: Larastan\\Larastan\\Properties\\ModelCastHelper}, {class: Larastan\\Larastan\\Properties\\ModelPropertyHelper}, {class: Larastan\\Larastan\\Rules\\ModelRuleHelper}, {class: Larastan\\Larastan\\Methods\\BuilderHelper, arguments: {checkProperties: %checkModelProperties%}}, {class: Larastan\\Larastan\\Rules\\RelationExistenceRule, tags: [phpstan.rules.rule]}, {class: Larastan\\Larastan\\Rules\\CheckDispatchArgumentTypesCompatibleWithClassConstructorRule, arguments: {dispatchableClass: Illuminate\\Foundation\\Bus\\Dispatchable}, tags: [phpstan.rules.rule]}, {class: Larastan\\Larastan\\Rules\\CheckDispatchArgumentTypesCompatibleWithClassConstructorRule, arguments: {dispatchableClass: Illuminate\\Foundation\\Events\\Dispatchable}, tags: [phpstan.rules.rule]}, {class: Larastan\\Larastan\\Properties\\Schema\\MySqlDataTypeToPhpTypeConverter}, {class: Larastan\\Larastan\\LarastanStubFilesExtension, tags: [phpstan.stubFilesExtension]}, {class: Larastan\\Larastan\\Rules\\UnusedViewsRule}, {class: Larastan\\Larastan\\Collectors\\UsedViewFunctionCollector, tags: [phpstan.collector]}, {class: Larastan\\Larastan\\Collectors\\UsedEmailViewCollector, tags: [phpstan.collector]}, {class: Larastan\\Larastan\\Collectors\\UsedViewMakeCollector, tags: [phpstan.collector]}, {class: Larastan\\Larastan\\Collectors\\UsedViewFacadeMakeCollector, tags: [phpstan.collector]}, {class: Larastan\\Larastan\\Collectors\\UsedRouteFacadeViewCollector, tags: [phpstan.collector]}, {class: Larastan\\Larastan\\Collectors\\UsedViewInAnotherViewCollector}, {class: Larastan\\Larastan\\Support\\ViewFileHelper, arguments: {viewDirectories: %viewDirectories%}}, {class: Larastan\\Larastan\\Support\\ViewParser, arguments: {parser: @currentPhpVersionSimpleDirectParser}}, {class: Larastan\\Larastan\\Rules\\NoMissingTranslationsRule, arguments: {translationDirectories: %translationDirectories%}}, {class: Larastan\\Larastan\\Collectors\\UsedTranslationFunctionCollector, tags: [phpstan.collector]}, {class: Larastan\\Larastan\\Collectors\\UsedTranslationTranslatorCollector, tags: [phpstan.collector]}, {class: Larastan\\Larastan\\Collectors\\UsedTranslationFacadeCollector, tags: [phpstan.collector]}, {class: Larastan\\Larastan\\Collectors\\UsedTranslationViewCollector}, {class: Larastan\\Larastan\\ReturnTypes\\ApplicationMakeDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ContainerMakeDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ConsoleCommand\\ArgumentDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ConsoleCommand\\HasArgumentDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ConsoleCommand\\OptionDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ConsoleCommand\\HasOptionDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\TranslatorGetReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\LangGetReturnTypeExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\TransHelperReturnTypeExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\DoubleUnderscoreHelperReturnTypeExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\AppMakeHelper}, {class: Larastan\\Larastan\\Internal\\ConsoleApplicationResolver}, {class: Larastan\\Larastan\\Internal\\ConsoleApplicationHelper}, {class: Larastan\\Larastan\\Support\\HigherOrderCollectionProxyHelper}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\ConfigFunctionDynamicFunctionReturnTypeExtension}, {class: Larastan\\Larastan\\ReturnTypes\\ConfigRepositoryDynamicMethodReturnTypeExtension}, {class: Larastan\\Larastan\\ReturnTypes\\ConfigFacadeCollectionDynamicStaticMethodReturnTypeExtension}, {class: Larastan\\Larastan\\Support\\ConfigParser, arguments: {parser: @currentPhpVersionSimpleDirectParser, configPaths: %configDirectories%}}, {class: Larastan\\Larastan\\Internal\\ConfigHelper}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\EnvFunctionDynamicFunctionReturnTypeExtension}, {class: Larastan\\Larastan\\ReturnTypes\\FormRequestSafeDynamicMethodReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\Rules\\NoAuthFacadeInRequestScopeRule}, {class: Larastan\\Larastan\\Rules\\NoAuthHelperInRequestScopeRule}, {class: Larastan\\Larastan\\Rules\\ConfigCollectionRule}, {class: Illuminate\\Filesystem\\Filesystem, autowired: self}, migrationsParser: {class: PHPStan\\Parser\\CachedParser, arguments: {originalParser: @currentPhpVersionSimpleDirectParser, cachedNodesByStringCountMax: %cache.nodesByStringCountMax%}, autowired: false}, {class: Carbon\\PHPStan\\MacroExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}}}',
  'analysedPaths' => 
  array (
    0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app',
    1 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\routes',
    2 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\config',
    3 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\database',
    4 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests',
  ),
  'scannedFiles' => 
  array (
  ),
  'composerLocks' => 
  array (
    'C:/laragon/www/TenRusl-Payment-Webhook-sim/composer.lock' => '774626239567513946c461d9a02af8ddcd8f7c8a',
  ),
  'composerInstalled' => 
  array (
    'C:/laragon/www/TenRusl-Payment-Webhook-sim/vendor/composer/installed.php' => 
    array (
      'versions' => 
      array (
        'barryvdh/laravel-ide-helper' => 
        array (
          'pretty_version' => 'v3.6.0',
          'version' => '3.6.0.0',
          'reference' => '8d00250cba25728373e92c1d8dcebcbf64623d29',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../barryvdh/laravel-ide-helper',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'barryvdh/reflection-docblock' => 
        array (
          'pretty_version' => 'v2.4.0',
          'version' => '2.4.0.0',
          'reference' => 'd103774cbe7e94ddee7e4870f97f727b43fe7201',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../barryvdh/reflection-docblock',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'bepsvpt/secure-headers' => 
        array (
          'pretty_version' => '9.0.0',
          'version' => '9.0.0.0',
          'reference' => '7efbc3d8b988051b5ff81c4cacd1d12e875528ed',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../bepsvpt/secure-headers',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'brianium/paratest' => 
        array (
          'pretty_version' => 'v7.8.4',
          'version' => '7.8.4.0',
          'reference' => '130a9bf0e269ee5f5b320108f794ad03e275cad4',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../brianium/paratest',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'brick/math' => 
        array (
          'pretty_version' => '0.14.1',
          'version' => '0.14.1.0',
          'reference' => 'f05858549e5f9d7bb45875a75583240a38a281d0',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../brick/math',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'carbonphp/carbon-doctrine-types' => 
        array (
          'pretty_version' => '3.2.0',
          'version' => '3.2.0.0',
          'reference' => '18ba5ddfec8976260ead6e866180bd5d2f71aa1d',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../carbonphp/carbon-doctrine-types',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'composer/class-map-generator' => 
        array (
          'pretty_version' => '1.7.0',
          'version' => '1.7.0.0',
          'reference' => '2373419b7709815ed323ebf18c3c72d03ff4a8a6',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/./class-map-generator',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'composer/pcre' => 
        array (
          'pretty_version' => '3.3.2',
          'version' => '3.3.2.0',
          'reference' => 'b2bed4734f0cc156ee1fe9c0da2550420d99a21e',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/./pcre',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'cordoval/hamcrest-php' => 
        array (
          'dev_requirement' => true,
          'replaced' => 
          array (
            0 => '*',
          ),
        ),
        'darkaonline/l5-swagger' => 
        array (
          'pretty_version' => '9.0.1',
          'version' => '9.0.1.0',
          'reference' => '2c26427f8c41db8e72232415e7287313e6b6a2e2',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../darkaonline/l5-swagger',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'davedevelopment/hamcrest-php' => 
        array (
          'dev_requirement' => true,
          'replaced' => 
          array (
            0 => '*',
          ),
        ),
        'dflydev/dot-access-data' => 
        array (
          'pretty_version' => 'v3.0.3',
          'version' => '3.0.3.0',
          'reference' => 'a23a2bf4f31d3518f3ecb38660c95715dfead60f',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../dflydev/dot-access-data',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'doctrine/annotations' => 
        array (
          'pretty_version' => '2.0.2',
          'version' => '2.0.2.0',
          'reference' => '901c2ee5d26eb64ff43c47976e114bf00843acf7',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../doctrine/annotations',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'doctrine/deprecations' => 
        array (
          'pretty_version' => '1.1.5',
          'version' => '1.1.5.0',
          'reference' => '459c2f5dd3d6a4633d3b5f46ee2b1c40f57d3f38',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../doctrine/deprecations',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'doctrine/inflector' => 
        array (
          'pretty_version' => '2.1.0',
          'version' => '2.1.0.0',
          'reference' => '6d6c96277ea252fc1304627204c3d5e6e15faa3b',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../doctrine/inflector',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'doctrine/lexer' => 
        array (
          'pretty_version' => '3.0.1',
          'version' => '3.0.1.0',
          'reference' => '31ad66abc0fc9e1a1f2d9bc6a42668d2fbbcd6dd',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../doctrine/lexer',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'dragonmantank/cron-expression' => 
        array (
          'pretty_version' => 'v3.6.0',
          'version' => '3.6.0.0',
          'reference' => 'd61a8a9604ec1f8c3d150d09db6ce98b32675013',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../dragonmantank/cron-expression',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'egulias/email-validator' => 
        array (
          'pretty_version' => '4.0.4',
          'version' => '4.0.4.0',
          'reference' => 'd42c8731f0624ad6bdc8d3e5e9a4524f68801cfa',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../egulias/email-validator',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'fakerphp/faker' => 
        array (
          'pretty_version' => 'v1.24.1',
          'version' => '1.24.1.0',
          'reference' => 'e0ee18eb1e6dc3cda3ce9fd97e5a0689a88a64b5',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../fakerphp/faker',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'fidry/cpu-core-counter' => 
        array (
          'pretty_version' => '1.3.0',
          'version' => '1.3.0.0',
          'reference' => 'db9508f7b1474469d9d3c53b86f817e344732678',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../fidry/cpu-core-counter',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'filp/whoops' => 
        array (
          'pretty_version' => '2.18.4',
          'version' => '2.18.4.0',
          'reference' => 'd2102955e48b9fd9ab24280a7ad12ed552752c4d',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../filp/whoops',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'fruitcake/php-cors' => 
        array (
          'pretty_version' => 'v1.3.0',
          'version' => '1.3.0.0',
          'reference' => '3d158f36e7875e2f040f37bc0573956240a5a38b',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../fruitcake/php-cors',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'graham-campbell/result-type' => 
        array (
          'pretty_version' => 'v1.1.3',
          'version' => '1.1.3.0',
          'reference' => '3ba905c11371512af9d9bdd27d99b782216b6945',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../graham-campbell/result-type',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'guzzlehttp/guzzle' => 
        array (
          'pretty_version' => '7.10.0',
          'version' => '7.10.0.0',
          'reference' => 'b51ac707cfa420b7bfd4e4d5e510ba8008e822b4',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../guzzlehttp/guzzle',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'guzzlehttp/promises' => 
        array (
          'pretty_version' => '2.3.0',
          'version' => '2.3.0.0',
          'reference' => '481557b130ef3790cf82b713667b43030dc9c957',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../guzzlehttp/promises',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'guzzlehttp/psr7' => 
        array (
          'pretty_version' => '2.8.0',
          'version' => '2.8.0.0',
          'reference' => '21dc724a0583619cd1652f673303492272778051',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../guzzlehttp/psr7',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'guzzlehttp/uri-template' => 
        array (
          'pretty_version' => 'v1.0.5',
          'version' => '1.0.5.0',
          'reference' => '4f4bbd4e7172148801e76e3decc1e559bdee34e1',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../guzzlehttp/uri-template',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'hamcrest/hamcrest-php' => 
        array (
          'pretty_version' => 'v2.1.1',
          'version' => '2.1.1.0',
          'reference' => 'f8b1c0173b22fa6ec77a81fe63e5b01eba7e6487',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../hamcrest/hamcrest-php',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'iamcal/sql-parser' => 
        array (
          'pretty_version' => 'v0.6',
          'version' => '0.6.0.0',
          'reference' => '947083e2dca211a6f12fb1beb67a01e387de9b62',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../iamcal/sql-parser',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'illuminate/auth' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.41.1',
          ),
        ),
        'illuminate/broadcasting' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.41.1',
          ),
        ),
        'illuminate/bus' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.41.1',
          ),
        ),
        'illuminate/cache' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.41.1',
          ),
        ),
        'illuminate/collections' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.41.1',
          ),
        ),
        'illuminate/concurrency' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.41.1',
          ),
        ),
        'illuminate/conditionable' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.41.1',
          ),
        ),
        'illuminate/config' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.41.1',
          ),
        ),
        'illuminate/console' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.41.1',
          ),
        ),
        'illuminate/container' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.41.1',
          ),
        ),
        'illuminate/contracts' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.41.1',
          ),
        ),
        'illuminate/cookie' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.41.1',
          ),
        ),
        'illuminate/database' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.41.1',
          ),
        ),
        'illuminate/encryption' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.41.1',
          ),
        ),
        'illuminate/events' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.41.1',
          ),
        ),
        'illuminate/filesystem' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.41.1',
          ),
        ),
        'illuminate/hashing' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.41.1',
          ),
        ),
        'illuminate/http' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.41.1',
          ),
        ),
        'illuminate/json-schema' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.41.1',
          ),
        ),
        'illuminate/log' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.41.1',
          ),
        ),
        'illuminate/macroable' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.41.1',
          ),
        ),
        'illuminate/mail' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.41.1',
          ),
        ),
        'illuminate/notifications' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.41.1',
          ),
        ),
        'illuminate/pagination' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.41.1',
          ),
        ),
        'illuminate/pipeline' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.41.1',
          ),
        ),
        'illuminate/process' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.41.1',
          ),
        ),
        'illuminate/queue' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.41.1',
          ),
        ),
        'illuminate/redis' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.41.1',
          ),
        ),
        'illuminate/routing' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.41.1',
          ),
        ),
        'illuminate/session' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.41.1',
          ),
        ),
        'illuminate/support' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.41.1',
          ),
        ),
        'illuminate/testing' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.41.1',
          ),
        ),
        'illuminate/translation' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.41.1',
          ),
        ),
        'illuminate/validation' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.41.1',
          ),
        ),
        'illuminate/view' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.41.1',
          ),
        ),
        'jean85/pretty-package-versions' => 
        array (
          'pretty_version' => '2.1.1',
          'version' => '2.1.1.0',
          'reference' => '4d7aa5dab42e2a76d99559706022885de0e18e1a',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../jean85/pretty-package-versions',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'kodova/hamcrest-php' => 
        array (
          'dev_requirement' => true,
          'replaced' => 
          array (
            0 => '*',
          ),
        ),
        'larastan/larastan' => 
        array (
          'pretty_version' => 'v3.8.0',
          'version' => '3.8.0.0',
          'reference' => 'd13ef96d652d1b2a8f34f1760ba6bf5b9c98112e',
          'type' => 'phpstan-extension',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../larastan/larastan',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'laravel/framework' => 
        array (
          'pretty_version' => 'v12.41.1',
          'version' => '12.41.1.0',
          'reference' => '3e229b05935fd0300c632fb1f718c73046d664fc',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../laravel/framework',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel/pail' => 
        array (
          'pretty_version' => 'v1.2.4',
          'version' => '1.2.4.0',
          'reference' => '49f92285ff5d6fc09816e976a004f8dec6a0ea30',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../laravel/pail',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'laravel/pint' => 
        array (
          'pretty_version' => 'v1.26.0',
          'version' => '1.26.0.0',
          'reference' => '69dcca060ecb15e4b564af63d1f642c81a241d6f',
          'type' => 'project',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../laravel/pint',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'laravel/prompts' => 
        array (
          'pretty_version' => 'v0.3.8',
          'version' => '0.3.8.0',
          'reference' => '096748cdfb81988f60090bbb839ce3205ace0d35',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../laravel/prompts',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel/sail' => 
        array (
          'pretty_version' => 'v1.49.0',
          'version' => '1.49.0.0',
          'reference' => '070c7f34ca8dbece4350fbfe0bab580047dfacc7',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../laravel/sail',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'laravel/serializable-closure' => 
        array (
          'pretty_version' => 'v2.0.7',
          'version' => '2.0.7.0',
          'reference' => 'cb291e4c998ac50637c7eeb58189c14f5de5b9dd',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../laravel/serializable-closure',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel/tinker' => 
        array (
          'pretty_version' => 'v2.10.2',
          'version' => '2.10.2.0',
          'reference' => '3bcb5f62d6f837e0f093a601e26badafb127bd4c',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../laravel/tinker',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'league/commonmark' => 
        array (
          'pretty_version' => '2.8.0',
          'version' => '2.8.0.0',
          'reference' => '4efa10c1e56488e658d10adf7b7b7dcd19940bfb',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../league/commonmark',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'league/config' => 
        array (
          'pretty_version' => 'v1.2.0',
          'version' => '1.2.0.0',
          'reference' => '754b3604fb2984c71f4af4a9cbe7b57f346ec1f3',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../league/config',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'league/flysystem' => 
        array (
          'pretty_version' => '3.30.2',
          'version' => '3.30.2.0',
          'reference' => '5966a8ba23e62bdb518dd9e0e665c2dbd4b5b277',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../league/flysystem',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'league/flysystem-local' => 
        array (
          'pretty_version' => '3.30.2',
          'version' => '3.30.2.0',
          'reference' => 'ab4f9d0d672f601b102936aa728801dd1a11968d',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../league/flysystem-local',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'league/mime-type-detection' => 
        array (
          'pretty_version' => '1.16.0',
          'version' => '1.16.0.0',
          'reference' => '2d6702ff215bf922936ccc1ad31007edc76451b9',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../league/mime-type-detection',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'league/uri' => 
        array (
          'pretty_version' => '7.6.0',
          'version' => '7.6.0.0',
          'reference' => 'f625804987a0a9112d954f9209d91fec52182344',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../league/uri',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'league/uri-interfaces' => 
        array (
          'pretty_version' => '7.6.0',
          'version' => '7.6.0.0',
          'reference' => 'ccbfb51c0445298e7e0b7f4481b942f589665368',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../league/uri-interfaces',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'mockery/mockery' => 
        array (
          'pretty_version' => '1.6.12',
          'version' => '1.6.12.0',
          'reference' => '1f4efdd7d3beafe9807b08156dfcb176d18f1699',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../mockery/mockery',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'monolog/monolog' => 
        array (
          'pretty_version' => '3.9.0',
          'version' => '3.9.0.0',
          'reference' => '10d85740180ecba7896c87e06a166e0c95a0e3b6',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../monolog/monolog',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'mtdowling/cron-expression' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => '^1.0',
          ),
        ),
        'myclabs/deep-copy' => 
        array (
          'pretty_version' => '1.13.4',
          'version' => '1.13.4.0',
          'reference' => '07d290f0c47959fd5eed98c95ee5602db07e0b6a',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../myclabs/deep-copy',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'nesbot/carbon' => 
        array (
          'pretty_version' => '3.10.3',
          'version' => '3.10.3.0',
          'reference' => '8e3643dcd149ae0fe1d2ff4f2c8e4bbfad7c165f',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../nesbot/carbon',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'nette/schema' => 
        array (
          'pretty_version' => 'v1.3.3',
          'version' => '1.3.3.0',
          'reference' => '2befc2f42d7c715fd9d95efc31b1081e5d765004',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../nette/schema',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'nette/utils' => 
        array (
          'pretty_version' => 'v4.1.0',
          'version' => '4.1.0.0',
          'reference' => 'fa1f0b8261ed150447979eb22e373b7b7ad5a8e0',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../nette/utils',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'nikic/php-parser' => 
        array (
          'pretty_version' => 'v5.6.2',
          'version' => '5.6.2.0',
          'reference' => '3a454ca033b9e06b63282ce19562e892747449bb',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../nikic/php-parser',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'nunomaduro/collision' => 
        array (
          'pretty_version' => 'v8.8.3',
          'version' => '8.8.3.0',
          'reference' => '1dc9e88d105699d0fee8bb18890f41b274f6b4c4',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../nunomaduro/collision',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'nunomaduro/termwind' => 
        array (
          'pretty_version' => 'v2.3.3',
          'version' => '2.3.3.0',
          'reference' => '6fb2a640ff502caace8e05fd7be3b503a7e1c017',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../nunomaduro/termwind',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'pestphp/pest' => 
        array (
          'pretty_version' => 'v3.8.4',
          'version' => '3.8.4.0',
          'reference' => '72cf695554420e21858cda831d5db193db102574',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../pestphp/pest',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'pestphp/pest-plugin' => 
        array (
          'pretty_version' => 'v3.0.0',
          'version' => '3.0.0.0',
          'reference' => 'e79b26c65bc11c41093b10150c1341cc5cdbea83',
          'type' => 'composer-plugin',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../pestphp/pest-plugin',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'pestphp/pest-plugin-arch' => 
        array (
          'pretty_version' => 'v3.1.1',
          'version' => '3.1.1.0',
          'reference' => 'db7bd9cb1612b223e16618d85475c6f63b9c8daa',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../pestphp/pest-plugin-arch',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'pestphp/pest-plugin-laravel' => 
        array (
          'pretty_version' => 'v3.2.0',
          'version' => '3.2.0.0',
          'reference' => '6801be82fd92b96e82dd72e563e5674b1ce365fc',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../pestphp/pest-plugin-laravel',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'pestphp/pest-plugin-mutate' => 
        array (
          'pretty_version' => 'v3.0.5',
          'version' => '3.0.5.0',
          'reference' => 'e10dbdc98c9e2f3890095b4fe2144f63a5717e08',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../pestphp/pest-plugin-mutate',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phar-io/manifest' => 
        array (
          'pretty_version' => '2.0.4',
          'version' => '2.0.4.0',
          'reference' => '54750ef60c58e43759730615a392c31c80e23176',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../phar-io/manifest',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phar-io/version' => 
        array (
          'pretty_version' => '3.2.1',
          'version' => '3.2.1.0',
          'reference' => '4f7fd7836c6f332bb2933569e566a0d6c4cbed74',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../phar-io/version',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpdocumentor/reflection-common' => 
        array (
          'pretty_version' => '2.2.0',
          'version' => '2.2.0.0',
          'reference' => '1d01c49d4ed62f25aa84a747ad35d5a16924662b',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../phpdocumentor/reflection-common',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpdocumentor/reflection-docblock' => 
        array (
          'pretty_version' => '5.6.5',
          'version' => '5.6.5.0',
          'reference' => '90614c73d3800e187615e2dd236ad0e2a01bf761',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../phpdocumentor/reflection-docblock',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpdocumentor/type-resolver' => 
        array (
          'pretty_version' => '1.12.0',
          'version' => '1.12.0.0',
          'reference' => '92a98ada2b93d9b201a613cb5a33584dde25f195',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../phpdocumentor/type-resolver',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpoption/phpoption' => 
        array (
          'pretty_version' => '1.9.4',
          'version' => '1.9.4.0',
          'reference' => '638a154f8d4ee6a5cfa96d6a34dfbe0cffa9566d',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../phpoption/phpoption',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'phpstan/phpdoc-parser' => 
        array (
          'pretty_version' => '2.3.0',
          'version' => '2.3.0.0',
          'reference' => '1e0cd5370df5dd2e556a36b9c62f62e555870495',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../phpstan/phpdoc-parser',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpstan/phpstan' => 
        array (
          'pretty_version' => '2.1.32',
          'version' => '2.1.32.0',
          'reference' => 'e126cad1e30a99b137b8ed75a85a676450ebb227',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../phpstan/phpstan',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpunit/php-code-coverage' => 
        array (
          'pretty_version' => '11.0.11',
          'version' => '11.0.11.0',
          'reference' => '4f7722aa9a7b76aa775e2d9d4e95d1ea16eeeef4',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../phpunit/php-code-coverage',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpunit/php-file-iterator' => 
        array (
          'pretty_version' => '5.1.0',
          'version' => '5.1.0.0',
          'reference' => '118cfaaa8bc5aef3287bf315b6060b1174754af6',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../phpunit/php-file-iterator',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpunit/php-invoker' => 
        array (
          'pretty_version' => '5.0.1',
          'version' => '5.0.1.0',
          'reference' => 'c1ca3814734c07492b3d4c5f794f4b0995333da2',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../phpunit/php-invoker',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpunit/php-text-template' => 
        array (
          'pretty_version' => '4.0.1',
          'version' => '4.0.1.0',
          'reference' => '3e0404dc6b300e6bf56415467ebcb3fe4f33e964',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../phpunit/php-text-template',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpunit/php-timer' => 
        array (
          'pretty_version' => '7.0.1',
          'version' => '7.0.1.0',
          'reference' => '3b415def83fbcb41f991d9ebf16ae4ad8b7837b3',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../phpunit/php-timer',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpunit/phpunit' => 
        array (
          'pretty_version' => '11.5.33',
          'version' => '11.5.33.0',
          'reference' => '5965e9ff57546cb9137c0ff6aa78cb7442b05cf6',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../phpunit/phpunit',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'psr/cache' => 
        array (
          'pretty_version' => '3.0.0',
          'version' => '3.0.0.0',
          'reference' => 'aa5030cfa5405eccfdcb1083ce040c2cb8d253bf',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../psr/cache',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'psr/clock' => 
        array (
          'pretty_version' => '1.0.0',
          'version' => '1.0.0.0',
          'reference' => 'e41a24703d4560fd0acb709162f73b8adfc3aa0d',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../psr/clock',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'psr/clock-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '1.0',
          ),
        ),
        'psr/container' => 
        array (
          'pretty_version' => '2.0.2',
          'version' => '2.0.2.0',
          'reference' => 'c71ecc56dfe541dbd90c5360474fbc405f8d5963',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../psr/container',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'psr/container-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '1.1|2.0',
          ),
        ),
        'psr/event-dispatcher' => 
        array (
          'pretty_version' => '1.0.0',
          'version' => '1.0.0.0',
          'reference' => 'dbefd12671e8a14ec7f180cab83036ed26714bb0',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../psr/event-dispatcher',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'psr/event-dispatcher-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '1.0',
          ),
        ),
        'psr/http-client' => 
        array (
          'pretty_version' => '1.0.3',
          'version' => '1.0.3.0',
          'reference' => 'bb5906edc1c324c9a05aa0873d40117941e5fa90',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../psr/http-client',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'psr/http-client-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '1.0',
          ),
        ),
        'psr/http-factory' => 
        array (
          'pretty_version' => '1.1.0',
          'version' => '1.1.0.0',
          'reference' => '2b4765fddfe3b508ac62f829e852b1501d3f6e8a',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../psr/http-factory',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'psr/http-factory-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '1.0',
          ),
        ),
        'psr/http-message' => 
        array (
          'pretty_version' => '2.0',
          'version' => '2.0.0.0',
          'reference' => '402d35bcb92c70c026d1a6a9883f06b2ead23d71',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../psr/http-message',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'psr/http-message-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '1.0',
          ),
        ),
        'psr/log' => 
        array (
          'pretty_version' => '3.0.2',
          'version' => '3.0.2.0',
          'reference' => 'f16e1d5863e37f8d8c2a01719f5b34baa2b714d3',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../psr/log',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'psr/log-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '1.0|2.0|3.0',
            1 => '3.0.0',
          ),
        ),
        'psr/simple-cache' => 
        array (
          'pretty_version' => '3.0.0',
          'version' => '3.0.0.0',
          'reference' => '764e0b3939f5ca87cb904f570ef9be2d78a07865',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../psr/simple-cache',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'psr/simple-cache-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '1.0|2.0|3.0',
          ),
        ),
        'psy/psysh' => 
        array (
          'pretty_version' => 'v0.12.15',
          'version' => '0.12.15.0',
          'reference' => '38953bc71491c838fcb6ebcbdc41ab7483cd549c',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../psy/psysh',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'ralouphie/getallheaders' => 
        array (
          'pretty_version' => '3.0.3',
          'version' => '3.0.3.0',
          'reference' => '120b605dfeb996808c31b6477290a714d356e822',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../ralouphie/getallheaders',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'ramsey/collection' => 
        array (
          'pretty_version' => '2.1.1',
          'version' => '2.1.1.0',
          'reference' => '344572933ad0181accbf4ba763e85a0306a8c5e2',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../ramsey/collection',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'ramsey/uuid' => 
        array (
          'pretty_version' => '4.9.1',
          'version' => '4.9.1.0',
          'reference' => '81f941f6f729b1e3ceea61d9d014f8b6c6800440',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../ramsey/uuid',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'rhumsaa/uuid' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => '4.9.1',
          ),
        ),
        'sebastian/cli-parser' => 
        array (
          'pretty_version' => '3.0.2',
          'version' => '3.0.2.0',
          'reference' => '15c5dd40dc4f38794d383bb95465193f5e0ae180',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../sebastian/cli-parser',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/code-unit' => 
        array (
          'pretty_version' => '3.0.3',
          'version' => '3.0.3.0',
          'reference' => '54391c61e4af8078e5b276ab082b6d3c54c9ad64',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../sebastian/code-unit',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/code-unit-reverse-lookup' => 
        array (
          'pretty_version' => '4.0.1',
          'version' => '4.0.1.0',
          'reference' => '183a9b2632194febd219bb9246eee421dad8d45e',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../sebastian/code-unit-reverse-lookup',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/comparator' => 
        array (
          'pretty_version' => '6.3.2',
          'version' => '6.3.2.0',
          'reference' => '85c77556683e6eee4323e4c5468641ca0237e2e8',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../sebastian/comparator',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/complexity' => 
        array (
          'pretty_version' => '4.0.1',
          'version' => '4.0.1.0',
          'reference' => 'ee41d384ab1906c68852636b6de493846e13e5a0',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../sebastian/complexity',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/diff' => 
        array (
          'pretty_version' => '6.0.2',
          'version' => '6.0.2.0',
          'reference' => 'b4ccd857127db5d41a5b676f24b51371d76d8544',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../sebastian/diff',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/environment' => 
        array (
          'pretty_version' => '7.2.1',
          'version' => '7.2.1.0',
          'reference' => 'a5c75038693ad2e8d4b6c15ba2403532647830c4',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../sebastian/environment',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/exporter' => 
        array (
          'pretty_version' => '6.3.2',
          'version' => '6.3.2.0',
          'reference' => '70a298763b40b213ec087c51c739efcaa90bcd74',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../sebastian/exporter',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/global-state' => 
        array (
          'pretty_version' => '7.0.2',
          'version' => '7.0.2.0',
          'reference' => '3be331570a721f9a4b5917f4209773de17f747d7',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../sebastian/global-state',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/lines-of-code' => 
        array (
          'pretty_version' => '3.0.1',
          'version' => '3.0.1.0',
          'reference' => 'd36ad0d782e5756913e42ad87cb2890f4ffe467a',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../sebastian/lines-of-code',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/object-enumerator' => 
        array (
          'pretty_version' => '6.0.1',
          'version' => '6.0.1.0',
          'reference' => 'f5b498e631a74204185071eb41f33f38d64608aa',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../sebastian/object-enumerator',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/object-reflector' => 
        array (
          'pretty_version' => '4.0.1',
          'version' => '4.0.1.0',
          'reference' => '6e1a43b411b2ad34146dee7524cb13a068bb35f9',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../sebastian/object-reflector',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/recursion-context' => 
        array (
          'pretty_version' => '6.0.3',
          'version' => '6.0.3.0',
          'reference' => 'f6458abbf32a6c8174f8f26261475dc133b3d9dc',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../sebastian/recursion-context',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/type' => 
        array (
          'pretty_version' => '5.1.3',
          'version' => '5.1.3.0',
          'reference' => 'f77d2d4e78738c98d9a68d2596fe5e8fa380f449',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../sebastian/type',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/version' => 
        array (
          'pretty_version' => '5.0.2',
          'version' => '5.0.2.0',
          'reference' => 'c687e3387b99f5b03b6caa64c74b63e2936ff874',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../sebastian/version',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'spatie/laravel-csp' => 
        array (
          'pretty_version' => '3.21.0',
          'version' => '3.21.0.0',
          'reference' => '1c5a878dddc66283d80ff3bbe810c9dcda4ee919',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../spatie/laravel-csp',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'spatie/laravel-package-tools' => 
        array (
          'pretty_version' => '1.92.7',
          'version' => '1.92.7.0',
          'reference' => 'f09a799850b1ed765103a4f0b4355006360c49a5',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../spatie/laravel-package-tools',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'spatie/once' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => '*',
          ),
        ),
        'staabm/side-effects-detector' => 
        array (
          'pretty_version' => '1.0.5',
          'version' => '1.0.5.0',
          'reference' => 'd8334211a140ce329c13726d4a715adbddd0a163',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../staabm/side-effects-detector',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'swagger-api/swagger-ui' => 
        array (
          'pretty_version' => 'v5.30.3',
          'version' => '5.30.3.0',
          'reference' => '199761a94d03753ec62c23bb4ba162bb73c3cfc7',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../swagger-api/swagger-ui',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'symfony/clock' => 
        array (
          'pretty_version' => 'v7.4.0',
          'version' => '7.4.0.0',
          'reference' => '9169f24776edde469914c1e7a1442a50f7a4e110',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../symfony/clock',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/console' => 
        array (
          'pretty_version' => 'v7.4.0',
          'version' => '7.4.0.0',
          'reference' => '0bc0f45254b99c58d45a8fbf9fb955d46cbd1bb8',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../symfony/console',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/css-selector' => 
        array (
          'pretty_version' => 'v7.4.0',
          'version' => '7.4.0.0',
          'reference' => 'ab862f478513e7ca2fe9ec117a6f01a8da6e1135',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../symfony/css-selector',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/deprecation-contracts' => 
        array (
          'pretty_version' => 'v3.6.0',
          'version' => '3.6.0.0',
          'reference' => '63afe740e99a13ba87ec199bb07bbdee937a5b62',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../symfony/deprecation-contracts',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/error-handler' => 
        array (
          'pretty_version' => 'v7.4.0',
          'version' => '7.4.0.0',
          'reference' => '48be2b0653594eea32dcef130cca1c811dcf25c2',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../symfony/error-handler',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/event-dispatcher' => 
        array (
          'pretty_version' => 'v7.4.0',
          'version' => '7.4.0.0',
          'reference' => '9dddcddff1ef974ad87b3708e4b442dc38b2261d',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../symfony/event-dispatcher',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/event-dispatcher-contracts' => 
        array (
          'pretty_version' => 'v3.6.0',
          'version' => '3.6.0.0',
          'reference' => '59eb412e93815df44f05f342958efa9f46b1e586',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../symfony/event-dispatcher-contracts',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/event-dispatcher-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '2.0|3.0',
          ),
        ),
        'symfony/finder' => 
        array (
          'pretty_version' => 'v7.4.0',
          'version' => '7.4.0.0',
          'reference' => '340b9ed7320570f319028a2cbec46d40535e94bd',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../symfony/finder',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/http-foundation' => 
        array (
          'pretty_version' => 'v7.4.0',
          'version' => '7.4.0.0',
          'reference' => '769c1720b68e964b13b58529c17d4a385c62167b',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../symfony/http-foundation',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/http-kernel' => 
        array (
          'pretty_version' => 'v7.4.0',
          'version' => '7.4.0.0',
          'reference' => '7348193cd384495a755554382e4526f27c456085',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../symfony/http-kernel',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/mailer' => 
        array (
          'pretty_version' => 'v7.4.0',
          'version' => '7.4.0.0',
          'reference' => 'a3d9eea8cfa467ece41f0f54ba28185d74bd53fd',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../symfony/mailer',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/mime' => 
        array (
          'pretty_version' => 'v7.4.0',
          'version' => '7.4.0.0',
          'reference' => 'bdb02729471be5d047a3ac4a69068748f1a6be7a',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../symfony/mime',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-ctype' => 
        array (
          'pretty_version' => 'v1.33.0',
          'version' => '1.33.0.0',
          'reference' => 'a3cc8b044a6ea513310cbd48ef7333b384945638',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../symfony/polyfill-ctype',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-intl-grapheme' => 
        array (
          'pretty_version' => 'v1.33.0',
          'version' => '1.33.0.0',
          'reference' => '380872130d3a5dd3ace2f4010d95125fde5d5c70',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../symfony/polyfill-intl-grapheme',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-intl-idn' => 
        array (
          'pretty_version' => 'v1.33.0',
          'version' => '1.33.0.0',
          'reference' => '9614ac4d8061dc257ecc64cba1b140873dce8ad3',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../symfony/polyfill-intl-idn',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-intl-normalizer' => 
        array (
          'pretty_version' => 'v1.33.0',
          'version' => '1.33.0.0',
          'reference' => '3833d7255cc303546435cb650316bff708a1c75c',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../symfony/polyfill-intl-normalizer',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-mbstring' => 
        array (
          'pretty_version' => 'v1.33.0',
          'version' => '1.33.0.0',
          'reference' => '6d857f4d76bd4b343eac26d6b539585d2bc56493',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../symfony/polyfill-mbstring',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-php80' => 
        array (
          'pretty_version' => 'v1.33.0',
          'version' => '1.33.0.0',
          'reference' => '0cc9dd0f17f61d8131e7df6b84bd344899fe2608',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../symfony/polyfill-php80',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-php83' => 
        array (
          'pretty_version' => 'v1.33.0',
          'version' => '1.33.0.0',
          'reference' => '17f6f9a6b1735c0f163024d959f700cfbc5155e5',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../symfony/polyfill-php83',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-php84' => 
        array (
          'pretty_version' => 'v1.33.0',
          'version' => '1.33.0.0',
          'reference' => 'd8ced4d875142b6a7426000426b8abc631d6b191',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../symfony/polyfill-php84',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-php85' => 
        array (
          'pretty_version' => 'v1.33.0',
          'version' => '1.33.0.0',
          'reference' => 'd4e5fcd4ab3d998ab16c0db48e6cbb9a01993f91',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../symfony/polyfill-php85',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-uuid' => 
        array (
          'pretty_version' => 'v1.33.0',
          'version' => '1.33.0.0',
          'reference' => '21533be36c24be3f4b1669c4725c7d1d2bab4ae2',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../symfony/polyfill-uuid',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/process' => 
        array (
          'pretty_version' => 'v7.4.0',
          'version' => '7.4.0.0',
          'reference' => '7ca8dc2d0dcf4882658313aba8be5d9fd01026c8',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../symfony/process',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/routing' => 
        array (
          'pretty_version' => 'v7.4.0',
          'version' => '7.4.0.0',
          'reference' => '4720254cb2644a0b876233d258a32bf017330db7',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../symfony/routing',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/service-contracts' => 
        array (
          'pretty_version' => 'v3.6.1',
          'version' => '3.6.1.0',
          'reference' => '45112560a3ba2d715666a509a0bc9521d10b6c43',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../symfony/service-contracts',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/string' => 
        array (
          'pretty_version' => 'v7.4.0',
          'version' => '7.4.0.0',
          'reference' => 'd50e862cb0a0e0886f73ca1f31b865efbb795003',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../symfony/string',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/translation' => 
        array (
          'pretty_version' => 'v7.4.0',
          'version' => '7.4.0.0',
          'reference' => '2d01ca0da3f092f91eeedb46f24aa30d2fca8f68',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../symfony/translation',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/translation-contracts' => 
        array (
          'pretty_version' => 'v3.6.1',
          'version' => '3.6.1.0',
          'reference' => '65a8bc82080447fae78373aa10f8d13b38338977',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../symfony/translation-contracts',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/translation-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '2.3|3.0',
          ),
        ),
        'symfony/uid' => 
        array (
          'pretty_version' => 'v7.4.0',
          'version' => '7.4.0.0',
          'reference' => '2498e9f81b7baa206f44de583f2f48350b90142c',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../symfony/uid',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/var-dumper' => 
        array (
          'pretty_version' => 'v7.4.0',
          'version' => '7.4.0.0',
          'reference' => '41fd6c4ae28c38b294b42af6db61446594a0dece',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../symfony/var-dumper',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/yaml' => 
        array (
          'pretty_version' => 'v7.4.0',
          'version' => '7.4.0.0',
          'reference' => '6c84a4b55aee4cd02034d1c528e83f69ddf63810',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../symfony/yaml',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'ta-tikoma/phpunit-architecture-test' => 
        array (
          'pretty_version' => '0.8.5',
          'version' => '0.8.5.0',
          'reference' => 'cf6fb197b676ba716837c886baca842e4db29005',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../ta-tikoma/phpunit-architecture-test',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'theseer/tokenizer' => 
        array (
          'pretty_version' => '1.3.1',
          'version' => '1.3.1.0',
          'reference' => 'b7489ce515e168639d17feec34b8847c326b0b3c',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../theseer/tokenizer',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'tijsverkoyen/css-to-inline-styles' => 
        array (
          'pretty_version' => 'v2.3.0',
          'version' => '2.3.0.0',
          'reference' => '0d72ac1c00084279c1816675284073c5a337c20d',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../tijsverkoyen/css-to-inline-styles',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'vlucas/phpdotenv' => 
        array (
          'pretty_version' => 'v5.6.2',
          'version' => '5.6.2.0',
          'reference' => '24ac4c74f91ee2c193fa1aaa5c249cb0822809af',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../vlucas/phpdotenv',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'voku/portable-ascii' => 
        array (
          'pretty_version' => '2.0.3',
          'version' => '2.0.3.0',
          'reference' => 'b1d923f88091c6bf09699efcd7c8a1b1bfd7351d',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../voku/portable-ascii',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'webmozart/assert' => 
        array (
          'pretty_version' => '1.12.1',
          'version' => '1.12.1.0',
          'reference' => '9be6926d8b485f55b9229203f962b51ed377ba68',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../webmozart/assert',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'zircote/swagger-php' => 
        array (
          'pretty_version' => '5.7.5',
          'version' => '5.7.5.0',
          'reference' => '9a37739401485b42d779495e70548309820d11d6',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\composer/../zircote/swagger-php',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
      ),
    ),
  ),
  'executedFilesHashes' => 
  array (
    'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\larastan\\larastan\\bootstrap.php' => '28392079817075879815f110287690e80398fe5e',
    'phar://C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\phpstan\\phpstan\\phpstan.phar\\stubs\\runtime\\Attribute85.php' => '123dcd45f03f2463904087a66bfe2bc139760df0',
    'phar://C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\phpstan\\phpstan\\phpstan.phar\\stubs\\runtime\\ReflectionAttribute.php' => '0b4b78277eb6545955d2ce5e09bff28f1f8052c8',
    'phar://C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\phpstan\\phpstan\\phpstan.phar\\stubs\\runtime\\ReflectionIntersectionType.php' => 'a3e6299b87ee5d407dae7651758edfa11a74cb11',
    'phar://C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\vendor\\phpstan\\phpstan\\phpstan.phar\\stubs\\runtime\\ReflectionUnionType.php' => '1b349aa997a834faeafe05fa21bc31cae22bf2e2',
  ),
  'phpExtensions' => 
  array (
    0 => 'Core',
    1 => 'PDO',
    2 => 'Phar',
    3 => 'Reflection',
    4 => 'SPL',
    5 => 'SimpleXML',
    6 => 'bcmath',
    7 => 'calendar',
    8 => 'ctype',
    9 => 'curl',
    10 => 'date',
    11 => 'dom',
    12 => 'exif',
    13 => 'fileinfo',
    14 => 'filter',
    15 => 'ftp',
    16 => 'gd',
    17 => 'gmp',
    18 => 'hash',
    19 => 'iconv',
    20 => 'intl',
    21 => 'json',
    22 => 'libxml',
    23 => 'mbstring',
    24 => 'mysqli',
    25 => 'mysqlnd',
    26 => 'openssl',
    27 => 'pcre',
    28 => 'pdo_mysql',
    29 => 'pdo_pgsql',
    30 => 'pdo_sqlite',
    31 => 'pgsql',
    32 => 'random',
    33 => 'readline',
    34 => 'redis',
    35 => 'session',
    36 => 'sqlite3',
    37 => 'standard',
    38 => 'tokenizer',
    39 => 'xml',
    40 => 'xmlreader',
    41 => 'xmlwriter',
    42 => 'xsl',
    43 => 'zlib',
  ),
  'stubFiles' => 
  array (
  ),
  'level' => '5',
),
	'projectExtensionFiles' => array (
),
	'errorsCallback' => static function (): array { return array (
); },
	'locallyIgnoredErrorsCallback' => static function (): array { return array (
); },
	'linesToIgnore' => array (
),
	'unmatchedLineIgnores' => array (
),
	'collectedDataCallback' => static function (): array { return array (
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Console\\Commands\\RetryWebhookCommand.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureMethodCallCollector' => 
    array (
      0 => 
      array (
        0 => 
        array (
          0 => 'App\\Services\\Webhooks\\WebhookProcessor',
        ),
        1 => 'process',
        2 => 185,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Jobs\\ProcessWebhookEvent',
        1 => 'dispatch',
        2 => 177,
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Controllers\\Api\\V1\\PaymentsController.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\ConstructorWithoutImpurePointsCollector' => 
    array (
      0 => 'App\\Http\\Controllers\\Api\\V1\\PaymentsController',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Controllers\\Api\\V1\\WebhooksController.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\ConstructorWithoutImpurePointsCollector' => 
    array (
      0 => 'App\\Http\\Controllers\\Api\\V1\\WebhooksController',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Controllers\\Controller.php' => 
  array (
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Foundation\\Auth\\Access\\AuthorizesRequests',
        1 => 'Illuminate\\Foundation\\Validation\\ValidatesRequests',
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Controllers\\ProviderController.php' => 
  array (
    'Larastan\\Larastan\\Collectors\\UsedViewFunctionCollector' => 
    array (
      0 => 'pages.providers',
      1 => 'pages.show',
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'abort',
        1 => 38,
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Middleware\\CorrelationIdMiddleware.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Support\\Facades\\Context',
        1 => 'add',
        2 => 68,
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Requests\\Api\\V1\\CreatePaymentRequest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Http\\Requests\\Api\\V1\\CreatePaymentRequest',
        1 => 'authorize',
        2 => 'App\\Http\\Requests\\Api\\V1\\CreatePaymentRequest',
      ),
      1 => 
      array (
        0 => 'App\\Http\\Requests\\Api\\V1\\CreatePaymentRequest',
        1 => 'attributes',
        2 => 'App\\Http\\Requests\\Api\\V1\\CreatePaymentRequest',
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Requests\\Api\\V1\\WebhookRequest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Http\\Requests\\Api\\V1\\WebhookRequest',
        1 => 'authorize',
        2 => 'App\\Http\\Requests\\Api\\V1\\WebhookRequest',
      ),
      1 => 
      array (
        0 => 'App\\Http\\Requests\\Api\\V1\\WebhookRequest',
        1 => 'rules',
        2 => 'App\\Http\\Requests\\Api\\V1\\WebhookRequest',
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Jobs\\ProcessWebhookEvent.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\ConstructorWithoutImpurePointsCollector' => 
    array (
      0 => 'App\\Jobs\\ProcessWebhookEvent',
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureMethodCallCollector' => 
    array (
      0 => 
      array (
        0 => 
        array (
          0 => 'App\\Services\\Webhooks\\WebhookProcessor',
        ),
        1 => 'process',
        2 => 83,
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Foundation\\Bus\\Dispatchable',
      ),
      1 => 
      array (
        0 => 'Illuminate\\Queue\\InteractsWithQueue',
      ),
      2 => 
      array (
        0 => 'Illuminate\\Bus\\Queueable',
      ),
      3 => 
      array (
        0 => 'Illuminate\\Queue\\SerializesModels',
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Models\\Payment.php' => 
  array (
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
      1 => 
      array (
        0 => 'App\\Traits\\HasUlid',
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Models\\User.php' => 
  array (
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
        1 => 'Illuminate\\Notifications\\Notifiable',
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Models\\WebhookEvent.php' => 
  array (
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
      1 => 
      array (
        0 => 'App\\Traits\\HasUlid',
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Providers\\RouteServiceProvider.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Support\\Facades\\RateLimiter',
        1 => 'for',
        2 => 27,
      ),
      1 => 
      array (
        0 => 'Illuminate\\Support\\Facades\\RateLimiter',
        1 => 'for',
        2 => 34,
      ),
      2 => 
      array (
        0 => 'Illuminate\\Support\\Facades\\RateLimiter',
        1 => 'for',
        2 => 44,
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Repositories\\WebhookEventRepository.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureMethodCallCollector' => 
    array (
      0 => 
      array (
        0 => 
        array (
          0 => 'Illuminate\\Database\\Eloquent\\Model',
        ),
        1 => 'save',
        2 => 71,
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Idempotency\\IdempotencyKeyService.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\ConstructorWithoutImpurePointsCollector' => 
    array (
      0 => 'App\\Services\\Idempotency\\IdempotencyKeyService',
    ),
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Services\\Idempotency\\IdempotencyKeyService',
        1 => 'lockKey',
        2 => 'App\\Services\\Idempotency\\IdempotencyKeyService',
      ),
      1 => 
      array (
        0 => 'App\\Services\\Idempotency\\IdempotencyKeyService',
        1 => 'respKey',
        2 => 'App\\Services\\Idempotency\\IdempotencyKeyService',
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'ksort',
        1 => 171,
      ),
      1 => 
      array (
        0 => 'ksort',
        1 => 193,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Support\\Facades\\Cache',
        1 => 'forget',
        2 => 60,
      ),
      1 => 
      array (
        0 => 'Illuminate\\Support\\Facades\\Cache',
        1 => 'put',
        2 => 96,
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Idempotency\\RequestFingerprint.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'ksort',
        1 => 67,
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\AirwallexAdapter.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Services\\Payments\\Adapters\\AirwallexAdapter',
        1 => 'provider',
        2 => 'App\\Services\\Payments\\Adapters\\AirwallexAdapter',
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\AmazonBwpAdapter.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Services\\Payments\\Adapters\\AmazonBwpAdapter',
        1 => 'provider',
        2 => 'App\\Services\\Payments\\Adapters\\AmazonBwpAdapter',
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\DanaAdapter.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Services\\Payments\\Adapters\\DanaAdapter',
        1 => 'provider',
        2 => 'App\\Services\\Payments\\Adapters\\DanaAdapter',
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\DokuAdapter.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Services\\Payments\\Adapters\\DokuAdapter',
        1 => 'provider',
        2 => 'App\\Services\\Payments\\Adapters\\DokuAdapter',
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\LemonSqueezyAdapter.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Services\\Payments\\Adapters\\LemonSqueezyAdapter',
        1 => 'provider',
        2 => 'App\\Services\\Payments\\Adapters\\LemonSqueezyAdapter',
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\MidtransAdapter.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Services\\Payments\\Adapters\\MidtransAdapter',
        1 => 'provider',
        2 => 'App\\Services\\Payments\\Adapters\\MidtransAdapter',
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\MockAdapter.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Services\\Payments\\Adapters\\MockAdapter',
        1 => 'provider',
        2 => 'App\\Services\\Payments\\Adapters\\MockAdapter',
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\OyAdapter.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Services\\Payments\\Adapters\\OyAdapter',
        1 => 'provider',
        2 => 'App\\Services\\Payments\\Adapters\\OyAdapter',
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\PaddleAdapter.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Services\\Payments\\Adapters\\PaddleAdapter',
        1 => 'provider',
        2 => 'App\\Services\\Payments\\Adapters\\PaddleAdapter',
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\PayoneerAdapter.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Services\\Payments\\Adapters\\PayoneerAdapter',
        1 => 'provider',
        2 => 'App\\Services\\Payments\\Adapters\\PayoneerAdapter',
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\PaypalAdapter.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Services\\Payments\\Adapters\\PaypalAdapter',
        1 => 'provider',
        2 => 'App\\Services\\Payments\\Adapters\\PaypalAdapter',
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\SkrillAdapter.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Services\\Payments\\Adapters\\SkrillAdapter',
        1 => 'provider',
        2 => 'App\\Services\\Payments\\Adapters\\SkrillAdapter',
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\StripeAdapter.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Services\\Payments\\Adapters\\StripeAdapter',
        1 => 'provider',
        2 => 'App\\Services\\Payments\\Adapters\\StripeAdapter',
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\TripayAdapter.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Services\\Payments\\Adapters\\TripayAdapter',
        1 => 'provider',
        2 => 'App\\Services\\Payments\\Adapters\\TripayAdapter',
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\XenditAdapter.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Services\\Payments\\Adapters\\XenditAdapter',
        1 => 'provider',
        2 => 'App\\Services\\Payments\\Adapters\\XenditAdapter',
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\PaymentsService.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'ksort',
        1 => 96,
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Signatures\\PaddleSignature.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'ksort',
        1 => 115,
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Signatures\\SignatureVerifier.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'sort',
        1 => 124,
      ),
      1 => 
      array (
        0 => 'sort',
        1 => 130,
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Webhooks\\WebhookProcessor.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\ConstructorWithoutImpurePointsCollector' => 
    array (
      0 => 'App\\Services\\Webhooks\\WebhookProcessor',
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureMethodCallCollector' => 
    array (
      0 => 
      array (
        0 => 
        array (
          0 => 'App\\Repositories\\WebhookEventRepository',
        ),
        1 => 'touchAttempt',
        2 => 65,
      ),
      1 => 
      array (
        0 => 
        array (
          0 => 'App\\Repositories\\WebhookEventRepository',
        ),
        1 => 'touchAttempt',
        2 => 67,
      ),
      2 => 
      array (
        0 => 
        array (
          0 => 'App\\Repositories\\PaymentRepository',
        ),
        1 => 'updateStatusByProviderRef',
        2 => 101,
      ),
      3 => 
      array (
        0 => 
        array (
          0 => 'App\\Repositories\\WebhookEventRepository',
        ),
        1 => 'markProcessed',
        2 => 112,
      ),
      4 => 
      array (
        0 => 
        array (
          0 => 'App\\Repositories\\WebhookEventRepository',
        ),
        1 => 'markProcessed',
        2 => 142,
      ),
      5 => 
      array (
        0 => 
        array (
          0 => 'App\\Repositories\\WebhookEventRepository',
        ),
        1 => 'scheduleNextRetry',
        2 => 188,
      ),
      6 => 
      array (
        0 => 
        array (
          0 => 'App\\Repositories\\WebhookEventRepository',
        ),
        1 => 'markFailed',
        2 => 190,
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Support\\Json.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Support\\Json',
        1 => 'decode',
        2 => 53,
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Traits\\HasUlid.php' => 
  array (
    'PHPStan\\Rules\\Traits\\TraitDeclarationCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Traits\\HasUlid',
        1 => 15,
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\ValueObjects\\PaymentStatus.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\ValueObjects\\PaymentStatus',
        1 => 'isPending',
        2 => 'App\\ValueObjects\\PaymentStatus',
      ),
      1 => 
      array (
        0 => 'App\\ValueObjects\\PaymentStatus',
        1 => 'isSucceeded',
        2 => 'App\\ValueObjects\\PaymentStatus',
      ),
      2 => 
      array (
        0 => 'App\\ValueObjects\\PaymentStatus',
        1 => 'isFailed',
        2 => 'App\\ValueObjects\\PaymentStatus',
      ),
      3 => 
      array (
        0 => 'App\\ValueObjects\\PaymentStatus',
        1 => 'isFinal',
        2 => 'App\\ValueObjects\\PaymentStatus',
      ),
      4 => 
      array (
        0 => 'App\\ValueObjects\\PaymentStatus',
        1 => 'toString',
        2 => 'App\\ValueObjects\\PaymentStatus',
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\database\\migrations\\2025_11_13_000001_alter_payments_add_provider_and_meta.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Database\\Connection',
        1 => 'statement',
        2 => 35,
      ),
      1 => 
      array (
        0 => 'Illuminate\\Database\\Connection',
        1 => 'statement',
        2 => 40,
      ),
      2 => 
      array (
        0 => 'Illuminate\\Database\\Connection',
        1 => 'statement',
        2 => 63,
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\database\\migrations\\2025_11_13_000002_alter_webhook_events_rename_attempt_count_add_audit_timestamps_payment_status.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Database\\Connection',
        1 => 'statement',
        2 => 58,
      ),
      1 => 
      array (
        0 => 'Illuminate\\Database\\Connection',
        1 => 'statement',
        2 => 90,
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\routes\\api.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Support\\Facades\\Route',
        1 => 'fallback',
        2 => 93,
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\routes\\web.php' => 
  array (
    'Larastan\\Larastan\\Collectors\\UsedViewFunctionCollector' => 
    array (
      0 => 'main.index',
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Support\\Facades\\Route',
        1 => 'redirect',
        2 => 39,
      ),
      1 => 
      array (
        0 => 'Illuminate\\Support\\Facades\\Route',
        1 => 'get',
        2 => 41,
      ),
      2 => 
      array (
        0 => 'Illuminate\\Support\\Facades\\Route',
        1 => 'fallback',
        2 => 64,
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\CreatesApplication.php' => 
  array (
    'PHPStan\\Rules\\Traits\\TraitDeclarationCollector' => 
    array (
      0 => 
      array (
        0 => 'Tests\\CreatesApplication',
        1 => 9,
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\PaymentsApiTest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'uses',
        1 => 11,
      ),
      1 => 
      array (
        0 => 'it',
        1 => 13,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureMethodCallCollector' => 
    array (
      0 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeIn',
        2 => 27,
      ),
      1 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBe',
        2 => 34,
      ),
      2 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeString',
        2 => 37,
      ),
      3 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeGreaterThan',
        2 => 38,
      ),
      4 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBe',
        2 => 52,
      ),
      5 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBe',
        2 => 53,
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Payments\\CreatePaymentTest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'uses',
        1 => 10,
      ),
      1 => 
      array (
        0 => 'it',
        1 => 12,
      ),
      2 => 
      array (
        0 => 'it',
        1 => 53,
      ),
      3 => 
      array (
        0 => 'it',
        1 => 88,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureMethodCallCollector' => 
    array (
      0 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeIn',
        2 => 30,
      ),
      1 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBe',
        2 => 49,
      ),
      2 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBe',
        2 => 50,
      ),
      3 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeIn',
        2 => 69,
      ),
      4 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeIn',
        2 => 70,
      ),
      5 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeString',
        2 => 76,
      ),
      6 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeGreaterThan',
        2 => 77,
      ),
      7 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeString',
        2 => 79,
      ),
      8 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeGreaterThan',
        2 => 80,
      ),
      9 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBe',
        2 => 82,
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Payments\\GetPaymentStatusTest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'uses',
        1 => 11,
      ),
      1 => 
      array (
        0 => 'it',
        1 => 13,
      ),
      2 => 
      array (
        0 => 'it',
        1 => 48,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureMethodCallCollector' => 
    array (
      0 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBe',
        2 => 44,
      ),
      1 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBe',
        2 => 45,
      ),
      2 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeIn',
        2 => 54,
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\WebhookReceiverTest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'uses',
        1 => 17,
      ),
      1 => 
      array (
        0 => 'it',
        1 => 27,
      ),
      2 => 
      array (
        0 => 'it',
        1 => 31,
      ),
      3 => 
      array (
        0 => 'it',
        1 => 41,
      ),
      4 => 
      array (
        0 => 'it',
        1 => 63,
      ),
      5 => 
      array (
        0 => 'it',
        1 => 109,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureMethodCallCollector' => 
    array (
      0 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBe',
        2 => 38,
      ),
      1 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBe',
        2 => 60,
      ),
      2 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeIn',
        2 => 82,
      ),
      3 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeIn',
        2 => 90,
      ),
      4 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBe',
        2 => 98,
      ),
      5 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeGreaterThanOrEqual',
        2 => 106,
      ),
      6 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeNull',
        2 => 196,
      ),
      7 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeNull',
        2 => 197,
      ),
      8 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeNull',
        2 => 198,
      ),
      9 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeGreaterThan',
        2 => 201,
      ),
      10 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeNull',
        2 => 202,
      ),
      11 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBe',
        2 => 205,
      ),
      12 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBe',
        2 => 208,
      ),
      13 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 209,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Support\\Facades\\Bus',
        1 => 'fake',
        2 => 110,
      ),
      1 => 
      array (
        0 => 'Illuminate\\Foundation\\Console\\Kernel',
        1 => 'call',
        2 => 181,
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\AirwallexWebhookTest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'uses',
        1 => 10,
      ),
      1 => 
      array (
        0 => 'it',
        1 => 12,
      ),
      2 => 
      array (
        0 => 'it',
        1 => 39,
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\AmazonBwpWebhookTest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'uses',
        1 => 10,
      ),
      1 => 
      array (
        0 => 'it',
        1 => 12,
      ),
      2 => 
      array (
        0 => 'it',
        1 => 36,
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\DanaWebhookTest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'uses',
        1 => 10,
      ),
      1 => 
      array (
        0 => 'it',
        1 => 12,
      ),
      2 => 
      array (
        0 => 'it',
        1 => 36,
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\DokuWebhookTest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'uses',
        1 => 10,
      ),
      1 => 
      array (
        0 => 'beforeEach',
        1 => 12,
      ),
      2 => 
      array (
        0 => 'config',
        1 => 13,
      ),
      3 => 
      array (
        0 => 'it',
        1 => 20,
      ),
      4 => 
      array (
        0 => 'it',
        1 => 67,
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\DuplicateEventTest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'uses',
        1 => 11,
      ),
      1 => 
      array (
        0 => 'beforeEach',
        1 => 13,
      ),
      2 => 
      array (
        0 => 'config',
        1 => 14,
      ),
      3 => 
      array (
        0 => 'it',
        1 => 17,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureMethodCallCollector' => 
    array (
      0 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBe',
        2 => 51,
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\LemonSqueezyWebhookTest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'uses',
        1 => 11,
      ),
      1 => 
      array (
        0 => 'beforeEach',
        1 => 13,
      ),
      2 => 
      array (
        0 => 'config',
        1 => 14,
      ),
      3 => 
      array (
        0 => 'it',
        1 => 17,
      ),
      4 => 
      array (
        0 => 'it',
        1 => 45,
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\MidtransWebhookTest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'it',
        1 => 7,
      ),
      1 => 
      array (
        0 => 'config',
        1 => 9,
      ),
      2 => 
      array (
        0 => 'it',
        1 => 38,
      ),
      3 => 
      array (
        0 => 'config',
        1 => 39,
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\MockWebhookTest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'it',
        1 => 7,
      ),
      1 => 
      array (
        0 => 'config',
        1 => 9,
      ),
      2 => 
      array (
        0 => 'it',
        1 => 37,
      ),
      3 => 
      array (
        0 => 'config',
        1 => 38,
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\OyWebhookTest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'uses',
        1 => 9,
      ),
      1 => 
      array (
        0 => 'beforeEach',
        1 => 11,
      ),
      2 => 
      array (
        0 => 'config',
        1 => 12,
      ),
      3 => 
      array (
        0 => 'it',
        1 => 19,
      ),
      4 => 
      array (
        0 => 'it',
        1 => 48,
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\PaddleWebhookTest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'uses',
        1 => 9,
      ),
      1 => 
      array (
        0 => 'beforeEach',
        1 => 11,
      ),
      2 => 
      array (
        0 => 'config',
        1 => 12,
      ),
      3 => 
      array (
        0 => 'it',
        1 => 19,
      ),
      4 => 
      array (
        0 => 'it',
        1 => 48,
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\PayoneerWebhookTest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'uses',
        1 => 9,
      ),
      1 => 
      array (
        0 => 'beforeEach',
        1 => 11,
      ),
      2 => 
      array (
        0 => 'config',
        1 => 12,
      ),
      3 => 
      array (
        0 => 'it',
        1 => 19,
      ),
      4 => 
      array (
        0 => 'it',
        1 => 48,
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\PaypalWebhookTest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'uses',
        1 => 11,
      ),
      1 => 
      array (
        0 => 'it',
        1 => 13,
      ),
      2 => 
      array (
        0 => 'it',
        1 => 35,
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\RetrySimulationTest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'it',
        1 => 9,
      ),
      1 => 
      array (
        0 => 'config',
        1 => 11,
      ),
      2 => 
      array (
        0 => 'it',
        1 => 35,
      ),
      3 => 
      array (
        0 => 'config',
        1 => 36,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureMethodCallCollector' => 
    array (
      0 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 30,
      ),
      1 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBe',
        2 => 54,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Foundation\\Console\\Kernel',
        1 => 'call',
        2 => 26,
      ),
      1 => 
      array (
        0 => 'Illuminate\\Foundation\\Console\\Kernel',
        1 => 'call',
        2 => 49,
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\SkrillWebhookTest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'uses',
        1 => 11,
      ),
      1 => 
      array (
        0 => 'it',
        1 => 13,
      ),
      2 => 
      array (
        0 => 'it',
        1 => 40,
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\StripeWebhookTest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'uses',
        1 => 11,
      ),
      1 => 
      array (
        0 => 'it',
        1 => 13,
      ),
      2 => 
      array (
        0 => 'it',
        1 => 37,
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\TripayWebhookTest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'uses',
        1 => 11,
      ),
      1 => 
      array (
        0 => 'it',
        1 => 13,
      ),
      2 => 
      array (
        0 => 'it',
        1 => 39,
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\XenditWebhookTest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'it',
        1 => 7,
      ),
      1 => 
      array (
        0 => 'config',
        1 => 10,
      ),
      2 => 
      array (
        0 => 'it',
        1 => 32,
      ),
      3 => 
      array (
        0 => 'config',
        1 => 33,
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Pest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureMethodCallCollector' => 
    array (
      0 => 
      array (
        0 => 
        array (
          0 => 'Pest\\PendingCalls\\UsesCall',
        ),
        1 => 'in',
        2 => 15,
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\TestCase.php' => 
  array (
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Tests\\CreatesApplication',
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Unit\\IdempotencyKeyServiceTest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'it',
        1 => 9,
      ),
      1 => 
      array (
        0 => 'it',
        1 => 23,
      ),
      2 => 
      array (
        0 => 'it',
        1 => 43,
      ),
      3 => 
      array (
        0 => 'it',
        1 => 59,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureMethodCallCollector' => 
    array (
      0 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBe',
        2 => 20,
      ),
      1 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeString',
        2 => 38,
      ),
      2 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeFalse',
        2 => 39,
      ),
      3 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBe',
        2 => 40,
      ),
      4 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeFalse',
        2 => 56,
      ),
      5 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeFalse',
        2 => 69,
      ),
      6 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeString',
        2 => 74,
      ),
      7 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeFalse',
        2 => 75,
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Unit\\RetryBackoffTest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'it',
        1 => 7,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureMethodCallCollector' => 
    array (
      0 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeNull',
        2 => 18,
      ),
      1 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeLessThanOrEqual',
        2 => 23,
      ),
      2 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeGreaterThanOrEqual',
        2 => 24,
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Unit\\SignatureVerifierTest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'it',
        1 => 8,
      ),
      1 => 
      array (
        0 => 'config',
        1 => 9,
      ),
      2 => 
      array (
        0 => 'it',
        1 => 28,
      ),
      3 => 
      array (
        0 => 'config',
        1 => 29,
      ),
      4 => 
      array (
        0 => 'it',
        1 => 43,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureMethodCallCollector' => 
    array (
      0 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 25,
      ),
      1 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeFalse',
        2 => 40,
      ),
      2 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeFalse',
        2 => 52,
      ),
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Unit\\TenRuslBootstrapTest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'it',
        1 => 5,
      ),
      1 => 
      array (
        0 => 'it',
        1 => 26,
      ),
      2 => 
      array (
        0 => 'config',
        1 => 28,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureMethodCallCollector' => 
    array (
      0 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeGreaterThan',
        2 => 10,
      ),
      1 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeGreaterThanOrEqual',
        2 => 11,
      ),
      2 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeGreaterThan',
        2 => 12,
      ),
      3 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 22,
      ),
      4 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBe',
        2 => 34,
      ),
      5 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBe',
        2 => 35,
      ),
      6 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBe',
        2 => 36,
      ),
    ),
  ),
); },
	'dependencies' => array (
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Console\\Commands\\RetryWebhookCommand.php' => 
  array (
    'fileHash' => 'a5ba76929139a6953d12d76b472c7b979287291b',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Console\\Kernel.php',
      1 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\routes\\console.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Console\\Commands\\ScaffoldViewsCommand.php' => 
  array (
    'fileHash' => 'ec99d1b952261acfa1d97f3ce28cc0f38374c591',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Console\\Kernel.php' => 
  array (
    'fileHash' => 'f04b373d3d2e1bf1287bb3ee611247a7acc8bc51',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Controllers\\Api\\V1\\PaymentsController.php' => 
  array (
    'fileHash' => '70e438d55081b6b8c5b8aeecb3181bef09bb6b4d',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\routes\\api.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Controllers\\Api\\V1\\WebhooksController.php' => 
  array (
    'fileHash' => 'a7aa827a06c90cd61b6127628a64f46e1a67872f',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\routes\\api.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Controllers\\Controller.php' => 
  array (
    'fileHash' => '0868cf6d2c567f6aa122346519407cc70d024954',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Controllers\\Api\\V1\\PaymentsController.php',
      1 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Controllers\\Api\\V1\\WebhooksController.php',
      2 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Controllers\\PaymentController.php',
      3 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Controllers\\ProviderController.php',
      4 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\routes\\api.php',
      5 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\routes\\web.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Controllers\\PaymentController.php' => 
  array (
    'fileHash' => '49a11a19e534aaf39072f6acfa5a6a4c5e9b754f',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\routes\\web.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Controllers\\ProviderController.php' => 
  array (
    'fileHash' => 'a52a72ad7db719d841914d86bc385cd4cfd06bd8',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\routes\\web.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Middleware\\CorrelationIdMiddleware.php' => 
  array (
    'fileHash' => 'bc217c83c2ca68b89f9dfa48dcb4c426a8175cea',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Middleware\\SecurityHeaders.php' => 
  array (
    'fileHash' => '905b2b4b12da5f0e8d3bf8aba10dc756de62c991',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Middleware\\SetLocale.php' => 
  array (
    'fileHash' => 'a668ae32f86a23f4f6dc224ae914ace2ef8d6501',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Middleware\\VerifyWebhookSignature.php' => 
  array (
    'fileHash' => '75dfb312801fea3f95b80bfbc485376fae361d0a',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\AirwallexWebhookTest.php',
      1 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\AmazonBwpWebhookTest.php',
      2 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\DanaWebhookTest.php',
      3 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\PaypalWebhookTest.php',
      4 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\SkrillWebhookTest.php',
      5 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\StripeWebhookTest.php',
      6 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\TripayWebhookTest.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Requests\\Api\\V1\\CreatePaymentRequest.php' => 
  array (
    'fileHash' => '41cf7e04b50780e99f338ec6dcc6bd884964cd9c',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Controllers\\Api\\V1\\PaymentsController.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Requests\\Api\\V1\\WebhookRequest.php' => 
  array (
    'fileHash' => 'ec7b4746a3175e69edf8062deb65ece45e35df29',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Controllers\\Api\\V1\\WebhooksController.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Resources\\Api\\V1\\PaymentResource.php' => 
  array (
    'fileHash' => '8fe5178060097a471da0c23e414c5dac19062edb',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Controllers\\Api\\V1\\PaymentsController.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Resources\\Api\\V1\\WebhookEventResource.php' => 
  array (
    'fileHash' => '1861552593637b568fd7451052c464e9bb735070',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Jobs\\ProcessWebhookEvent.php' => 
  array (
    'fileHash' => '701398ca2d23eac67960d69109a8bc6563e76352',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Console\\Commands\\RetryWebhookCommand.php',
      1 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\WebhookReceiverTest.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Models\\Payment.php' => 
  array (
    'fileHash' => 'e945a16cfde362d6671c2d7445c61aef2074c524',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Controllers\\Api\\V1\\PaymentsController.php',
      1 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Resources\\Api\\V1\\PaymentResource.php',
      2 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Repositories\\PaymentRepository.php',
      3 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Webhooks\\WebhookProcessor.php',
      4 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\database\\factories\\PaymentFactory.php',
      5 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\database\\seeders\\DatabaseSeeder.php',
      6 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Payments\\GetPaymentStatusTest.php',
      7 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\DuplicateEventTest.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Models\\User.php' => 
  array (
    'fileHash' => '52bef97d97946013f0b9b22d79ab507cf41ec229',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\config\\auth.php',
      1 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\database\\factories\\UserFactory.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Models\\WebhookEvent.php' => 
  array (
    'fileHash' => '975cdee06821c2b68ca1a85b9f93414475ebacf9',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Console\\Commands\\RetryWebhookCommand.php',
      1 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Resources\\Api\\V1\\WebhookEventResource.php',
      2 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Jobs\\ProcessWebhookEvent.php',
      3 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Repositories\\WebhookEventRepository.php',
      4 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\database\\factories\\WebhookEventFactory.php',
      5 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\database\\seeders\\DatabaseSeeder.php',
      6 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\WebhookReceiverTest.php',
      7 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\DuplicateEventTest.php',
      8 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\RetrySimulationTest.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\OpenApi\\Docs.php' => 
  array (
    'fileHash' => '2268f8649040cd2be9f20312beaa38955ed88c42',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Providers\\AppServiceProvider.php' => 
  array (
    'fileHash' => 'b70dcfe9c6df58f6a7fa2f4c7ec09a21bb027fad',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Providers\\RouteServiceProvider.php' => 
  array (
    'fileHash' => '6de10bc9b333907da4a7c4ac25d1fd30db3e6ebe',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Repositories\\PaymentRepository.php' => 
  array (
    'fileHash' => '5ba8e1ab6bd03db495181fcc38b2240d4527de85',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Controllers\\Api\\V1\\PaymentsController.php',
      1 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Providers\\AppServiceProvider.php',
      2 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Webhooks\\WebhookProcessor.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Repositories\\WebhookEventRepository.php' => 
  array (
    'fileHash' => '05e1c9cb106e04b2f7078a1cf634ef4af3e8f115',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Providers\\AppServiceProvider.php',
      1 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Webhooks\\WebhookProcessor.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Idempotency\\IdempotencyKeyService.php' => 
  array (
    'fileHash' => '345dfd0042faa9bd787ef73be25634337386bf7b',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Controllers\\Api\\V1\\PaymentsController.php',
      1 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Unit\\IdempotencyKeyServiceTest.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Idempotency\\RequestFingerprint.php' => 
  array (
    'fileHash' => 'c57dc3513e34eef15af1618ebcc56717be2b0a76',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Idempotency\\IdempotencyKeyService.php',
      1 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Unit\\IdempotencyKeyServiceTest.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\AirwallexAdapter.php' => 
  array (
    'fileHash' => 'ee24d2a87e8906b675f7c5925c981d3f8490c476',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\PaymentsService.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\AmazonBwpAdapter.php' => 
  array (
    'fileHash' => '9da561d6e3a2bf43c1b64c9e4fbeac4888159c83',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\PaymentsService.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\DanaAdapter.php' => 
  array (
    'fileHash' => '9d3df83f6d58713c710eb6a60b0928dde5f3bd34',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\PaymentsService.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\DokuAdapter.php' => 
  array (
    'fileHash' => '4d8c4e6707e4cc236b18f05fc2660a46abaa71ea',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\PaymentsService.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\LemonSqueezyAdapter.php' => 
  array (
    'fileHash' => 'e9bcfab1b48cbecd7e95860e7fbd2a238c51bbec',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\PaymentsService.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\MidtransAdapter.php' => 
  array (
    'fileHash' => '622bf54dcf3ac145c94262786ddc828b2024196c',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\PaymentsService.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\MockAdapter.php' => 
  array (
    'fileHash' => '9845f922ed6b44d09b1fb8b9fad52267eb9da563',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\PaymentsService.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\OyAdapter.php' => 
  array (
    'fileHash' => '5e84a8e9c2e1ee5ead547c9a7f419e303aaf8d17',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\PaymentsService.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\PaddleAdapter.php' => 
  array (
    'fileHash' => 'd869b15ff1a6e233619dc84743077539ddb0219a',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\PaymentsService.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\PayoneerAdapter.php' => 
  array (
    'fileHash' => '7c1406d1933020bbe374f34493cffc75fda4fed7',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\PaymentsService.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\PaypalAdapter.php' => 
  array (
    'fileHash' => '8d727df8523a828fc5e8a00e064c8560a3b750a4',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\PaymentsService.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\SkrillAdapter.php' => 
  array (
    'fileHash' => '1f58c1ac0da090cd1ea454e0ef431998d391c741',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\PaymentsService.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\StripeAdapter.php' => 
  array (
    'fileHash' => 'ed0a037ee98d172d0e6cdd7e06c7910c1320a2ec',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\PaymentsService.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\TripayAdapter.php' => 
  array (
    'fileHash' => 'ed7c5be1c10fb481ed4ce9901f543fae04b85293',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\PaymentsService.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\XenditAdapter.php' => 
  array (
    'fileHash' => '00518276ad8e330ed033852f29787eca2131a4e3',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\PaymentsService.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Contracts\\PaymentAdapter.php' => 
  array (
    'fileHash' => 'bc4f05e0fb47b85f2cd1da1f1d763b2910061204',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\AirwallexAdapter.php',
      1 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\AmazonBwpAdapter.php',
      2 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\DanaAdapter.php',
      3 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\DokuAdapter.php',
      4 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\LemonSqueezyAdapter.php',
      5 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\MidtransAdapter.php',
      6 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\MockAdapter.php',
      7 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\OyAdapter.php',
      8 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\PaddleAdapter.php',
      9 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\PayoneerAdapter.php',
      10 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\PaypalAdapter.php',
      11 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\SkrillAdapter.php',
      12 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\StripeAdapter.php',
      13 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\TripayAdapter.php',
      14 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\XenditAdapter.php',
      15 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\PaymentsService.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\PaymentsService.php' => 
  array (
    'fileHash' => '5e6eff9e293b3b5b5440fd24c085cfc816eaee88',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Controllers\\Api\\V1\\PaymentsController.php',
      1 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Controllers\\PaymentController.php',
      2 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Providers\\AppServiceProvider.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Signatures\\AirwallexSignature.php' => 
  array (
    'fileHash' => '174cc976388e1b804e1ac8ca2b968c2b8ccd9fa7',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Signatures\\SignatureVerifier.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Signatures\\AmazonBwpSignature.php' => 
  array (
    'fileHash' => '86d9b9917c6ad2b99a974d91b4519555c08fdfbc',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Signatures\\SignatureVerifier.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Signatures\\DanaSignature.php' => 
  array (
    'fileHash' => '08e318ecd5ddab744c89144da15137d5f6cf51ef',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Signatures\\SignatureVerifier.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Signatures\\DokuSignature.php' => 
  array (
    'fileHash' => 'a6c1a7ec2e840e4cb57e5062abde63c0a4d21f1c',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Signatures\\SignatureVerifier.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Signatures\\LemonSqueezySignature.php' => 
  array (
    'fileHash' => 'edb473e21dec91294382f7a10d432ded8c6e09b0',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Signatures\\SignatureVerifier.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Signatures\\MidtransSignature.php' => 
  array (
    'fileHash' => '8713b7968c52fadc1fa7e221aa5c312941579b77',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Signatures\\SignatureVerifier.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Signatures\\MockSignature.php' => 
  array (
    'fileHash' => '86af42edf0166cbb22e8968654e7f00972c7ad6c',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Signatures\\SignatureVerifier.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Signatures\\OySignature.php' => 
  array (
    'fileHash' => '4fcd8a09c764a2fae6a1e8393dc17ea756b7299a',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Signatures\\SignatureVerifier.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Signatures\\PaddleSignature.php' => 
  array (
    'fileHash' => '60d419a6572850a2f0f6c6dc09366df41bf246ef',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Signatures\\SignatureVerifier.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Signatures\\PayoneerSignature.php' => 
  array (
    'fileHash' => '519f1f33a6891366ae4f306c1ccb4c32e6916fb6',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Signatures\\SignatureVerifier.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Signatures\\PaypalSignature.php' => 
  array (
    'fileHash' => '406642dd782932634cebbb2d16ba9c736840b5a6',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Signatures\\SignatureVerifier.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Signatures\\SignatureVerifier.php' => 
  array (
    'fileHash' => 'c6e1566eddace4929f0f59389ee3c877ad044b82',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Middleware\\VerifyWebhookSignature.php',
      1 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Unit\\SignatureVerifierTest.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Signatures\\SkrillSignature.php' => 
  array (
    'fileHash' => 'ca2bd231da15a8d0b83edcecea55d45d95d7b39b',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Signatures\\SignatureVerifier.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Signatures\\StripeSignature.php' => 
  array (
    'fileHash' => '80067725019377919f87989faab002cd18b24760',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Signatures\\SignatureVerifier.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Signatures\\TripaySignature.php' => 
  array (
    'fileHash' => 'ae692077d64ac020233db9bdfaf05448adac29ca',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Signatures\\SignatureVerifier.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Signatures\\XenditSignature.php' => 
  array (
    'fileHash' => '41a6851c3bd623d7443acdd8a0e0de44435e0e21',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Signatures\\SignatureVerifier.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Webhooks\\RetryBackoff.php' => 
  array (
    'fileHash' => '580aac18b04d92bf06351b44d73c6687b534e816',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Console\\Commands\\RetryWebhookCommand.php',
      1 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Webhooks\\WebhookProcessor.php',
      2 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Unit\\RetryBackoffTest.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Webhooks\\WebhookProcessor.php' => 
  array (
    'fileHash' => 'e8c80b821c429bdfe6db6a7761c16f91fa3651a3',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Console\\Commands\\RetryWebhookCommand.php',
      1 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Controllers\\Api\\V1\\WebhooksController.php',
      2 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Jobs\\ProcessWebhookEvent.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Support\\Clock.php' => 
  array (
    'fileHash' => '503213c91846de0b47a2a3b0d316b0a683474a43',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Support\\Json.php' => 
  array (
    'fileHash' => 'c0ac0ee24b2456b242040febf197d110b0c9b96e',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Traits\\HasUlid.php' => 
  array (
    'fileHash' => 'c72519a1f61cc78269eecd363c1dbee16463e77e',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Console\\Commands\\RetryWebhookCommand.php',
      1 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Controllers\\Api\\V1\\PaymentsController.php',
      2 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Resources\\Api\\V1\\PaymentResource.php',
      3 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Resources\\Api\\V1\\WebhookEventResource.php',
      4 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Jobs\\ProcessWebhookEvent.php',
      5 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Models\\Payment.php',
      6 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Models\\WebhookEvent.php',
      7 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Repositories\\PaymentRepository.php',
      8 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Repositories\\WebhookEventRepository.php',
      9 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Webhooks\\WebhookProcessor.php',
      10 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\database\\factories\\PaymentFactory.php',
      11 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\database\\factories\\WebhookEventFactory.php',
      12 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\database\\seeders\\DatabaseSeeder.php',
      13 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Payments\\GetPaymentStatusTest.php',
      14 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\WebhookReceiverTest.php',
      15 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\DuplicateEventTest.php',
      16 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\RetrySimulationTest.php',
    ),
    'usedTraitDependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Models\\Payment.php',
      1 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Models\\WebhookEvent.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\ValueObjects\\PaymentStatus.php' => 
  array (
    'fileHash' => 'e04e890520cfd1c592415bbb18f9a2e00c98377b',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Console\\Commands\\RetryWebhookCommand.php',
      1 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Resources\\Api\\V1\\WebhookEventResource.php',
      2 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Jobs\\ProcessWebhookEvent.php',
      3 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Models\\Payment.php',
      4 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Models\\WebhookEvent.php',
      5 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Repositories\\PaymentRepository.php',
      6 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Repositories\\WebhookEventRepository.php',
      7 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Webhooks\\WebhookProcessor.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\config\\app.php' => 
  array (
    'fileHash' => '667d0cbfcb8ddc8e0b5639fc5a14c339bf02754c',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\config\\auth.php' => 
  array (
    'fileHash' => 'd14c6ca41850324dcf3bde4b8c4fe4635d21b02e',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\config\\cache.php' => 
  array (
    'fileHash' => 'cd1d6925c3548f3081f827bcce8f9b2c5522795e',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\config\\cors.php' => 
  array (
    'fileHash' => '8a7c44ea325c85cfffbb20844b31012fbf7a04f1',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\config\\csp.php' => 
  array (
    'fileHash' => '7fb23b041198e0258c28bad0335bbfbd7bcf34fb',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\config\\database.php' => 
  array (
    'fileHash' => '4d7bb78ce43539e75ede1418a37fd5b9cecbb718',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\config\\filesystems.php' => 
  array (
    'fileHash' => '6e1e66753542ecbccfe730cfee0d623723be2986',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\config\\ide-helper.php' => 
  array (
    'fileHash' => '7b3168e1198aa7d8ff310f8a705a2dc871452a5c',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\config\\l5-swagger.php' => 
  array (
    'fileHash' => '0e3d7c1ddb687bd201a3bce3d587e5f78c1704b2',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\config\\localization.php' => 
  array (
    'fileHash' => 'fd5f80ac9a8fc83b996429338d6a9d92dd049d9a',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\config\\logging.php' => 
  array (
    'fileHash' => 'f163e17e3d43b2aa18f20994b2d26c2ccabd5abc',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\config\\mail.php' => 
  array (
    'fileHash' => '55990e37cb337eee513173e5c48479cbb1e5202e',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\config\\queue.php' => 
  array (
    'fileHash' => 'd010c040a947e792dee55b6c5a74f14ba7f656d8',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\config\\secure-headers.php' => 
  array (
    'fileHash' => '769301cc82d53aa96c14e98b4975b51c65377265',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\config\\services.php' => 
  array (
    'fileHash' => 'db4fafeac1c3e89795de0bc27378ee2876cdb5f7',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\config\\session.php' => 
  array (
    'fileHash' => 'a0ce1b173c09908a3d698b26372566c604844a94',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\config\\tenrusl.php' => 
  array (
    'fileHash' => '04a128c2f5aebfb6df0412b63590981a78691a3b',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\database\\factories\\PaymentFactory.php' => 
  array (
    'fileHash' => 'a1c82f56d158ce72a6c5cb85861d464de90ab10f',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\database\\seeders\\DatabaseSeeder.php',
      1 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\DuplicateEventTest.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\database\\factories\\UserFactory.php' => 
  array (
    'fileHash' => '7ac74334b97dded2308b4265ca46014b317a82f9',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\database\\factories\\WebhookEventFactory.php' => 
  array (
    'fileHash' => 'aec69d9be810ab4e3f60becf07af4c9fba7aa274',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\database\\seeders\\DatabaseSeeder.php',
      1 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\RetrySimulationTest.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\database\\migrations\\0001_01_01_000000_create_users_table.php' => 
  array (
    'fileHash' => 'c83722f2f43dc31195e37312e72524af995c15a9',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\database\\migrations\\0001_01_01_000001_create_cache_table.php' => 
  array (
    'fileHash' => '1e63143baede25661ec2075259ba517cbf2c2400',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\database\\migrations\\0001_01_01_000002_create_jobs_table.php' => 
  array (
    'fileHash' => '61d635023428eaa5cc6f27e5b7f9683817125a50',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\database\\migrations\\2025_11_08_093720_create_payments_table.php' => 
  array (
    'fileHash' => 'ee3add33bcf9748b679521686d018220a73d2b66',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\database\\migrations\\2025_11_08_093730_create_webhook_events_table.php' => 
  array (
    'fileHash' => '68a0140ac81aa203b2c883a32cbebaba09032db2',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\database\\migrations\\2025_11_13_000001_alter_payments_add_provider_and_meta.php' => 
  array (
    'fileHash' => '6a75d15be22f830643aed82f90f7ffd6c2bc0cda',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\database\\migrations\\2025_11_13_000002_alter_webhook_events_rename_attempt_count_add_audit_timestamps_payment_status.php' => 
  array (
    'fileHash' => '8dc29b3a38bd591ca728bcf0bb9d28de9066a02e',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\database\\seeders\\DatabaseSeeder.php' => 
  array (
    'fileHash' => '624cfe6f61af9c97c3571e6c3684997aa9d4917b',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\routes\\api.php' => 
  array (
    'fileHash' => '2244b6b19e2fd5b7ffb0815a425ffa63b7e1bdfe',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\routes\\console.php' => 
  array (
    'fileHash' => '9b1c549c730701d27463d73b142a219ab1613b6f',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\routes\\web.php' => 
  array (
    'fileHash' => '89df0cdad439084b523a3afba0b5f4ab749eff91',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\CreatesApplication.php' => 
  array (
    'fileHash' => '8ccb3ae40fb91a49a8ee7ad6bc86ae3224d7f9b1',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\AirwallexWebhookTest.php',
      1 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\AmazonBwpWebhookTest.php',
      2 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\DanaWebhookTest.php',
      3 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\LemonSqueezyWebhookTest.php',
      4 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\PaypalWebhookTest.php',
      5 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\SkrillWebhookTest.php',
      6 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\StripeWebhookTest.php',
      7 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\TripayWebhookTest.php',
      8 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Pest.php',
      9 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\TestCase.php',
    ),
    'usedTraitDependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\TestCase.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\PaymentsApiTest.php' => 
  array (
    'fileHash' => '7c34890f8e008a27275d28e7d23862fbbdd1d861',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Payments\\CreatePaymentTest.php' => 
  array (
    'fileHash' => 'a9a6e1de654e718cc3001205ecbc632ac5ae6c09',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Payments\\GetPaymentStatusTest.php' => 
  array (
    'fileHash' => '8c715364b1c16234c47fa5cd077d6de01f4f10aa',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\WebhookReceiverTest.php' => 
  array (
    'fileHash' => 'ddf70527805c785da815dd47533db137c997cda7',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\AirwallexWebhookTest.php' => 
  array (
    'fileHash' => '8a1a711afcea4cc3ea820839f4962e0212d1a7cc',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\AmazonBwpWebhookTest.php' => 
  array (
    'fileHash' => 'a2eaa0041c75d97c85fa5e3d637324a3f8d748a8',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\DanaWebhookTest.php' => 
  array (
    'fileHash' => '1f492355e6c5a64224eb96eeba17f2317f06757f',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\DokuWebhookTest.php' => 
  array (
    'fileHash' => '23f9df1646df17465b4a0fd1c93a56867488d775',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\DuplicateEventTest.php' => 
  array (
    'fileHash' => '91cde7f52ba26c7f467aecf892689f4bd4205cff',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\LemonSqueezyWebhookTest.php' => 
  array (
    'fileHash' => '6a83d9b49f036e8bb320d8f93cec377ffda2df90',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\MidtransWebhookTest.php' => 
  array (
    'fileHash' => 'e099e0d860e7f849ee8bc15fe0a600e84e188a65',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\MockWebhookTest.php' => 
  array (
    'fileHash' => '7d5a3fe272079a96221d68d251bc0ec4c3021116',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\OyWebhookTest.php' => 
  array (
    'fileHash' => '130ea38d812be7dbccdc09acb44b992d0100375b',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\PaddleWebhookTest.php' => 
  array (
    'fileHash' => '014965e50ae632206487e9ed4a9b4e76b7affb53',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\PayoneerWebhookTest.php' => 
  array (
    'fileHash' => '014965e50ae632206487e9ed4a9b4e76b7affb53',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\PaypalWebhookTest.php' => 
  array (
    'fileHash' => 'ace62fc553ffcb874ccf3b1844e0cac774528e69',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\RetrySimulationTest.php' => 
  array (
    'fileHash' => 'a55c02abcf7ed8596ebff84b562a9d726b975148',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\SkrillWebhookTest.php' => 
  array (
    'fileHash' => 'cea5ecd9d9a1c8921c63e0f60b373c8279aad947',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\StripeWebhookTest.php' => 
  array (
    'fileHash' => '2fa70ebe9f115711a93830f1461052eb799f1426',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\TripayWebhookTest.php' => 
  array (
    'fileHash' => '6ceda13466f0c54b506014012f03e257a2917571',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\XenditWebhookTest.php' => 
  array (
    'fileHash' => '6d7fb2b36304834a227a6223c70bf294c71f7452',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Pest.php' => 
  array (
    'fileHash' => '2175c88b125a6f3eefd1c1b0d9cb22c36012dc78',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\TestCase.php' => 
  array (
    'fileHash' => '2f63eabf75d51a89111b9919e5e83e622c8798e8',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\AirwallexWebhookTest.php',
      1 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\AmazonBwpWebhookTest.php',
      2 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\DanaWebhookTest.php',
      3 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\LemonSqueezyWebhookTest.php',
      4 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\PaypalWebhookTest.php',
      5 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\SkrillWebhookTest.php',
      6 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\StripeWebhookTest.php',
      7 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\Webhooks\\TripayWebhookTest.php',
      8 => 'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Pest.php',
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Unit\\IdempotencyKeyServiceTest.php' => 
  array (
    'fileHash' => '933fb2e97ec1c0c470695f14e8acf2906652d0a9',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Unit\\RetryBackoffTest.php' => 
  array (
    'fileHash' => '3c834ee31199eca5799d786a5442ee7cd55ceb8d',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Unit\\SignatureVerifierTest.php' => 
  array (
    'fileHash' => '8e37852c23a552d67dba3bee9c990ffce53b76f4',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Unit\\TenRuslBootstrapTest.php' => 
  array (
    'fileHash' => 'ebf9a6cad67741b27e76bf5a6f043a69456b9b8b',
    'dependentFiles' => 
    array (
    ),
  ),
),
	'exportedNodesCallback' => static function (): array { return array (
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Console\\Commands\\RetryWebhookCommand.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Console\\Commands\\RetryWebhookCommand',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Retry engine:
 * - pilih event webhook yg "due" (next_retry_at <= now atau null)
 * - lakukan claiming atomik (lockForUpdate + update attempts/last_attempt_at/next_retry_at)
 * - proses inline atau dispatch job ke queue
 *
 * Kenapa harus ada claiming?
 * - supaya scheduler multi-run / multi-worker tidak memproses event yg sama bersamaan.
 * - next_retry_at berperan sebagai "lease" sekaligus jadwal retry berikutnya.
 *
 * Catatan:
 * - lockForUpdate wajib di dalam transaction agar benar-benar mengunci row. :contentReference[oaicite:2]{index=2}
 */',
         'namespace' => 'App\\Console\\Commands',
         'uses' => 
        array (
          'processwebhookevent' => 'App\\Jobs\\ProcessWebhookEvent',
          'webhookevent' => 'App\\Models\\WebhookEvent',
          'retrybackoff' => 'App\\Services\\Webhooks\\RetryBackoff',
          'webhookprocessor' => 'App\\Services\\Webhooks\\WebhookProcessor',
          'carbonimmutable' => 'Carbon\\CarbonImmutable',
          'command' => 'Illuminate\\Console\\Command',
          'collection' => 'Illuminate\\Support\\Collection',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'log' => 'Illuminate\\Support\\Facades\\Log',
          'throwable' => 'Throwable',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Console\\Command',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'signature',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'description',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'processor',
               'type' => 'App\\Services\\Webhooks\\WebhookProcessor',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Console\\Commands\\ScaffoldViewsCommand.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Console\\Commands\\ScaffoldViewsCommand',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Console\\Command',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'signature',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'description',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'fs',
               'type' => 'Illuminate\\Filesystem\\Filesystem',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Console\\Kernel.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Console\\Kernel',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Foundation\\Console\\Kernel',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'commands',
          ),
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Daftar command aplikasi.
     *
     * Catatan:
     * - Kalau kamu pakai auto-discovery command, list ini bisa dikosongkan.
     * - Tapi menaruhnya di sini itu “aman” dan eksplisit.
     *
     * @var array<class-string>
     */',
             'namespace' => 'App\\Console',
             'uses' => 
            array (
              'retrywebhookcommand' => 'App\\Console\\Commands\\RetryWebhookCommand',
              'schedule' => 'Illuminate\\Console\\Scheduling\\Schedule',
              'consolekernel' => 'Illuminate\\Foundation\\Console\\Kernel',
            ),
             'constUses' => 
            array (
            ),
          )),
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'schedule',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Definisikan jadwal tugas.
     *
     * Penting:
     * - Untuk menghindari double schedule, jadwal utama TenRusl ada di routes/console.php.
     * - Kalau kamu *memutuskan* memindahkan scheduling balik ke Kernel, pindahkan block
     *   Schedule::command(...) dari routes/console.php ke sini (dan hapus di routes/console.php).
     */',
             'namespace' => 'App\\Console',
             'uses' => 
            array (
              'retrywebhookcommand' => 'App\\Console\\Commands\\RetryWebhookCommand',
              'schedule' => 'Illuminate\\Console\\Scheduling\\Schedule',
              'consolekernel' => 'Illuminate\\Foundation\\Console\\Kernel',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'schedule',
               'type' => 'Illuminate\\Console\\Scheduling\\Schedule',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'commands',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Daftarkan command tambahan (jika diperlukan).
     *
     * Karena bootstrap/app.php sudah memuat routes/console.php, kita gunakan require_once
     * biar tidak kedobel kalau suatu saat ini juga dipanggil.
     */',
             'namespace' => 'App\\Console',
             'uses' => 
            array (
              'retrywebhookcommand' => 'App\\Console\\Commands\\RetryWebhookCommand',
              'schedule' => 'Illuminate\\Console\\Scheduling\\Schedule',
              'consolekernel' => 'Illuminate\\Foundation\\Console\\Kernel',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Controllers\\Api\\V1\\PaymentsController.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Http\\Controllers\\Api\\V1\\PaymentsController',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * PaymentsController (API v1)
 * --------------------------
 * Endpoint:
 * - POST   /api/v1/payments
 * - GET    /api/v1/payments/{provider}/{provider_ref}/status.
 *
 * Penting:
 * - store() WAJIB type-hint CreatePaymentRequest supaya validasi benar-benar dipakai.
 */',
         'namespace' => 'App\\Http\\Controllers\\Api\\V1',
         'uses' => 
        array (
          'controller' => 'App\\Http\\Controllers\\Controller',
          'createpaymentrequest' => 'App\\Http\\Requests\\Api\\V1\\CreatePaymentRequest',
          'paymentresource' => 'App\\Http\\Resources\\Api\\V1\\PaymentResource',
          'paymentrepository' => 'App\\Repositories\\PaymentRepository',
          'idempotencykeyservice' => 'App\\Services\\Idempotency\\IdempotencyKeyService',
          'paymentsservice' => 'App\\Services\\Payments\\PaymentsService',
          'jsonresponse' => 'Illuminate\\Http\\JsonResponse',
          'request' => 'Illuminate\\Http\\Request',
          'response' => 'Symfony\\Component\\HttpFoundation\\Response',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'App\\Http\\Controllers\\Controller',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__construct',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'payments',
               'type' => 'App\\Services\\Payments\\PaymentsService',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'paymentsRepo',
               'type' => 'App\\Repositories\\PaymentRepository',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'idemp',
               'type' => 'App\\Services\\Idempotency\\IdempotencyKeyService',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'store',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * POST /api/v1/payments
     * Buat pembayaran simulasi (idempotent).
     */',
             'namespace' => 'App\\Http\\Controllers\\Api\\V1',
             'uses' => 
            array (
              'controller' => 'App\\Http\\Controllers\\Controller',
              'createpaymentrequest' => 'App\\Http\\Requests\\Api\\V1\\CreatePaymentRequest',
              'paymentresource' => 'App\\Http\\Resources\\Api\\V1\\PaymentResource',
              'paymentrepository' => 'App\\Repositories\\PaymentRepository',
              'idempotencykeyservice' => 'App\\Services\\Idempotency\\IdempotencyKeyService',
              'paymentsservice' => 'App\\Services\\Payments\\PaymentsService',
              'jsonresponse' => 'Illuminate\\Http\\JsonResponse',
              'request' => 'Illuminate\\Http\\Request',
              'response' => 'Symfony\\Component\\HttpFoundation\\Response',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Http\\JsonResponse',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'request',
               'type' => 'App\\Http\\Requests\\Api\\V1\\CreatePaymentRequest',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'status',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * GET /api/v1/payments/{provider}/{provider_ref}/status.
     */',
             'namespace' => 'App\\Http\\Controllers\\Api\\V1',
             'uses' => 
            array (
              'controller' => 'App\\Http\\Controllers\\Controller',
              'createpaymentrequest' => 'App\\Http\\Requests\\Api\\V1\\CreatePaymentRequest',
              'paymentresource' => 'App\\Http\\Resources\\Api\\V1\\PaymentResource',
              'paymentrepository' => 'App\\Repositories\\PaymentRepository',
              'idempotencykeyservice' => 'App\\Services\\Idempotency\\IdempotencyKeyService',
              'paymentsservice' => 'App\\Services\\Payments\\PaymentsService',
              'jsonresponse' => 'Illuminate\\Http\\JsonResponse',
              'request' => 'Illuminate\\Http\\Request',
              'response' => 'Symfony\\Component\\HttpFoundation\\Response',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Http\\JsonResponse',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'request',
               'type' => 'Illuminate\\Http\\Request',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'provider',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'providerRef',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Controllers\\Api\\V1\\WebhooksController.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Http\\Controllers\\Api\\V1\\WebhooksController',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * WebhooksController (API v1)
 * --------------------------
 * Endpoint:
 * - POST /api/v1/webhooks/{provider}
 *
 * Penting:
 * - receive() WAJIB type-hint WebhookRequest supaya validasi benar-benar dipakai.
 * - Signature verification SUDAH dicegat oleh middleware \'verify.webhook.signature\'
 *   di routes/api.php (jadi yang masuk ke sini harus sudah lolos signature).
 */',
         'namespace' => 'App\\Http\\Controllers\\Api\\V1',
         'uses' => 
        array (
          'controller' => 'App\\Http\\Controllers\\Controller',
          'webhookrequest' => 'App\\Http\\Requests\\Api\\V1\\WebhookRequest',
          'webhookprocessor' => 'App\\Services\\Webhooks\\WebhookProcessor',
          'jsonresponse' => 'Illuminate\\Http\\JsonResponse',
          'arr' => 'Illuminate\\Support\\Arr',
          'str' => 'Illuminate\\Support\\Str',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'App\\Http\\Controllers\\Controller',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__construct',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'processor',
               'type' => 'App\\Services\\Webhooks\\WebhookProcessor',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'receive',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * POST /api/v1/webhooks/{provider}
     */',
             'namespace' => 'App\\Http\\Controllers\\Api\\V1',
             'uses' => 
            array (
              'controller' => 'App\\Http\\Controllers\\Controller',
              'webhookrequest' => 'App\\Http\\Requests\\Api\\V1\\WebhookRequest',
              'webhookprocessor' => 'App\\Services\\Webhooks\\WebhookProcessor',
              'jsonresponse' => 'Illuminate\\Http\\JsonResponse',
              'arr' => 'Illuminate\\Support\\Arr',
              'str' => 'Illuminate\\Support\\Str',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Http\\JsonResponse',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'request',
               'type' => 'App\\Http\\Requests\\Api\\V1\\WebhookRequest',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'provider',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Controllers\\Controller.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Http\\Controllers\\Controller',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Routing\\Controller',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'Illuminate\\Foundation\\Auth\\Access\\AuthorizesRequests',
        1 => 'Illuminate\\Foundation\\Validation\\ValidatesRequests',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Controllers\\PaymentController.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Http\\Controllers\\PaymentController',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'App\\Http\\Controllers\\Controller',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'providers',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Daftar provider aktif (untuk halaman/info sederhana via web route).
     */',
             'namespace' => 'App\\Http\\Controllers',
             'uses' => 
            array (
              'paymentsservice' => 'App\\Services\\Payments\\PaymentsService',
              'jsonresponse' => 'Illuminate\\Http\\JsonResponse',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Http\\JsonResponse',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'payments',
               'type' => 'App\\Services\\Payments\\PaymentsService',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'status',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Ambil status simulasi dari adapter (tanpa menyentuh DB).
     * Endpoint web sederhana; untuk API V1 gunakan controller API.
     */',
             'namespace' => 'App\\Http\\Controllers',
             'uses' => 
            array (
              'paymentsservice' => 'App\\Services\\Payments\\PaymentsService',
              'jsonresponse' => 'Illuminate\\Http\\JsonResponse',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Http\\JsonResponse',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'provider',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'providerRef',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'payments',
               'type' => 'App\\Services\\Payments\\PaymentsService',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Controllers\\ProviderController.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Http\\Controllers\\ProviderController',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'App\\Http\\Controllers\\Controller',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'index',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * List providers (catalog page).
     */',
             'namespace' => 'App\\Http\\Controllers',
             'uses' => 
            array (
              'request' => 'Illuminate\\Http\\Request',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'request',
               'type' => 'Illuminate\\Http\\Request',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'show',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Detail provider page.
     *
     * Sekarang:
     * - Langsung pakai view generik: resources/views/pages/show.blade.php
     * - Data utama (summary, docs, endpoints, signature_notes, example_payload)
     *   diambil dari resources/lang/{locale}/pages/{slug}.php
     */',
             'namespace' => 'App\\Http\\Controllers',
             'uses' => 
            array (
              'request' => 'Illuminate\\Http\\Request',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'request',
               'type' => 'Illuminate\\Http\\Request',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'provider',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'buildProvidersList',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Build daftar providers untuk halaman index.
     */',
             'namespace' => 'App\\Http\\Controllers',
             'uses' => 
            array (
              'request' => 'Illuminate\\Http\\Request',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'providerMeta',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Meta untuk satu provider (nama, logo, dll).
     */',
             'namespace' => 'App\\Http\\Controllers',
             'uses' => 
            array (
              'request' => 'Illuminate\\Http\\Request',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'slug',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Middleware\\CorrelationIdMiddleware.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Http\\Middleware\\CorrelationIdMiddleware',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * CorrelationIdMiddleware
 * -----------------------
 * Menjaga "X-Request-ID" konsisten di request & response untuk tracing/logging:
 *
 * - Jika client kirim X-Request-ID => pakai (setelah disanitasi)
 * - Kalau tidak ada => generate ULID
 * - Simpan ke request attribute (agar controller/service mudah akses)
 * - Inject ke Laravel Context (biar kebawa ke logs + bisa “nyambung” ke job)
 * - Tambah juga ke log context (fallback / kompatibilitas)
 * - Propagate ke response header X-Request-ID
 *
 * Idealnya middleware ini dipasang global supaya semua endpoint (payments, webhooks, dll)
 * punya trace id yang sama, sehingga log mudah ditelusuri.
 */',
         'namespace' => 'App\\Http\\Middleware',
         'uses' => 
        array (
          'closure' => 'Closure',
          'request' => 'Illuminate\\Http\\Request',
          'context' => 'Illuminate\\Support\\Facades\\Context',
          'log' => 'Illuminate\\Support\\Facades\\Log',
          'str' => 'Illuminate\\Support\\Str',
          'response' => 'Symfony\\Component\\HttpFoundation\\Response',
          'throwable' => 'Throwable',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedClassConstantsNode::__set_state(array(
           'constants' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedClassConstantNode::__set_state(array(
               'name' => 'HEADER',
               'value' => '\'X-Request-ID\'',
               'attributes' => 
              array (
              ),
            )),
          ),
           'public' => true,
           'private' => false,
           'final' => false,
           'phpDoc' => NULL,
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedClassConstantsNode::__set_state(array(
           'constants' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedClassConstantNode::__set_state(array(
               'name' => 'ATTR',
               'value' => '\'correlation_id\'',
               'attributes' => 
              array (
              ),
            )),
          ),
           'public' => true,
           'private' => false,
           'final' => false,
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Attribute key di request:
     * $request->attributes->get(self::ATTR)
     */',
             'namespace' => 'App\\Http\\Middleware',
             'uses' => 
            array (
              'closure' => 'Closure',
              'request' => 'Illuminate\\Http\\Request',
              'context' => 'Illuminate\\Support\\Facades\\Context',
              'log' => 'Illuminate\\Support\\Facades\\Log',
              'str' => 'Illuminate\\Support\\Str',
              'response' => 'Symfony\\Component\\HttpFoundation\\Response',
              'throwable' => 'Throwable',
            ),
             'constUses' => 
            array (
            ),
          )),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @param  \\Closure(\\Illuminate\\Http\\Request): \\Symfony\\Component\\HttpFoundation\\Response  $next
     */',
             'namespace' => 'App\\Http\\Middleware',
             'uses' => 
            array (
              'closure' => 'Closure',
              'request' => 'Illuminate\\Http\\Request',
              'context' => 'Illuminate\\Support\\Facades\\Context',
              'log' => 'Illuminate\\Support\\Facades\\Log',
              'str' => 'Illuminate\\Support\\Str',
              'response' => 'Symfony\\Component\\HttpFoundation\\Response',
              'throwable' => 'Throwable',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Symfony\\Component\\HttpFoundation\\Response',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'request',
               'type' => 'Illuminate\\Http\\Request',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'next',
               'type' => 'Closure',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Middleware\\SecurityHeaders.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Http\\Middleware\\SecurityHeaders',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Symfony\\Component\\HttpFoundation\\Response',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'request',
               'type' => 'Illuminate\\Http\\Request',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'next',
               'type' => 'Closure',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Middleware\\SetLocale.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Http\\Middleware\\SetLocale',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'request',
               'type' => 'Illuminate\\Http\\Request',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'next',
               'type' => 'Closure',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Middleware\\VerifyWebhookSignature.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Http\\Middleware\\VerifyWebhookSignature',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * VerifyWebhookSignature
 * ----------------------
 * Middleware "gate" sebelum masuk domain:
 * - Mengambil {provider} dari route /api/v1/webhooks/{provider}
 * - Baca raw body
 * - Verifikasi signature via SignatureVerifier
 * - Kalau gagal: STOP dan kembalikan 401 JSON
 *
 * Catatan:
 * - Middleware ini idealnya dipasang hanya untuk route webhook (bukan global),
 *   karena bukan semua endpoint membutuhkan signature verification.
 * - Raw body disimpan ke request attribute \'tenrusl_raw_body\' agar controller/
 *   FormRequest (WebhookRequest) bisa akses tanpa baca ulang stream.
 */',
         'namespace' => 'App\\Http\\Middleware',
         'uses' => 
        array (
          'signatureverifier' => 'App\\Services\\Signatures\\SignatureVerifier',
          'closure' => 'Closure',
          'jsonresponse' => 'Illuminate\\Http\\JsonResponse',
          'request' => 'Illuminate\\Http\\Request',
          'log' => 'Illuminate\\Support\\Facades\\Log',
          'response' => 'Symfony\\Component\\HttpFoundation\\Response',
          'throwable' => 'Throwable',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @param  \\Closure(\\Illuminate\\Http\\Request): (\\Symfony\\Component\\HttpFoundation\\Response)  $next
     */',
             'namespace' => 'App\\Http\\Middleware',
             'uses' => 
            array (
              'signatureverifier' => 'App\\Services\\Signatures\\SignatureVerifier',
              'closure' => 'Closure',
              'jsonresponse' => 'Illuminate\\Http\\JsonResponse',
              'request' => 'Illuminate\\Http\\Request',
              'log' => 'Illuminate\\Support\\Facades\\Log',
              'response' => 'Symfony\\Component\\HttpFoundation\\Response',
              'throwable' => 'Throwable',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Symfony\\Component\\HttpFoundation\\Response',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'request',
               'type' => 'Illuminate\\Http\\Request',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'next',
               'type' => 'Closure',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Requests\\Api\\V1\\CreatePaymentRequest.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Http\\Requests\\Api\\V1\\CreatePaymentRequest',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * FormRequest untuk:
 * - POST /api/v1/payments
 *
 * Tujuan:
 * - Validasi input sesuai dokumentasi (provider allowlist, amount, currency, dsb).
 * - Normalisasi input sebelum validasi:
 *   - "metadata" (client) -> "meta" (internal)
 *   - currency uppercase
 *   - default currency ke IDR
 *
 * Catatan tooling:
 * - Docblock @mixin + @method membantu static analyzer (Intelephense, PHPStan, Psalm).
 *
 * @mixin \\Illuminate\\Http\\Request
 *
 * @method bool has(string|array $key)
 * @method mixed input(string $key = null, mixed $default = null)
 * @method void merge(array $input)
 */',
         'namespace' => 'App\\Http\\Requests\\Api\\V1',
         'uses' => 
        array (
          'formrequest' => 'Illuminate\\Foundation\\Http\\FormRequest',
          'rule' => 'Illuminate\\Validation\\Rule',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Foundation\\Http\\FormRequest',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'authorize',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Endpoint ini public (simulator).
     * Kalau nanti ada auth, ubah jadi cek token/guard di sini.
     */',
             'namespace' => 'App\\Http\\Requests\\Api\\V1',
             'uses' => 
            array (
              'formrequest' => 'Illuminate\\Foundation\\Http\\FormRequest',
              'rule' => 'Illuminate\\Validation\\Rule',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'prepareForValidation',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Normalisasi input sebelum rules() dijalankan.
     *
     * - Jika client kirim "metadata" tapi tidak kirim "meta", kita map ke "meta".
     * - Currency kita uppercase (IDR, USD, dst).
     * - Jika currency kosong/tidak ada, kita default ke IDR.
     *
     * Ini membuat rule \'currency\' tetap "required", tapi request tanpa currency tetap lolos
     * karena di-inject IDR di tahap ini.
     */',
             'namespace' => 'App\\Http\\Requests\\Api\\V1',
             'uses' => 
            array (
              'formrequest' => 'Illuminate\\Foundation\\Http\\FormRequest',
              'rule' => 'Illuminate\\Validation\\Rule',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'rules',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Rules validasi request create payment.
     *
     * @return array<string, \\Illuminate\\Contracts\\Validation\\ValidationRule|array<mixed>|string>
     */',
             'namespace' => 'App\\Http\\Requests\\Api\\V1',
             'uses' => 
            array (
              'formrequest' => 'Illuminate\\Foundation\\Http\\FormRequest',
              'rule' => 'Illuminate\\Validation\\Rule',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'validated',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Pastikan output validated() selalu mengandung "meta" (internal),
     * dan tidak mengekspose "metadata" (alias).
     *
     * @param  array|int|string|null  $key
     * @param  mixed  $default
     * @return array<string, mixed>
     */',
             'namespace' => 'App\\Http\\Requests\\Api\\V1',
             'uses' => 
            array (
              'formrequest' => 'Illuminate\\Foundation\\Http\\FormRequest',
              'rule' => 'Illuminate\\Validation\\Rule',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'key',
               'type' => NULL,
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'default',
               'type' => NULL,
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'attributes',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Label attribute agar message validasi lebih rapi.
     *
     * @return array<string,string>
     */',
             'namespace' => 'App\\Http\\Requests\\Api\\V1',
             'uses' => 
            array (
              'formrequest' => 'Illuminate\\Foundation\\Http\\FormRequest',
              'rule' => 'Illuminate\\Validation\\Rule',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Requests\\Api\\V1\\WebhookRequest.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Http\\Requests\\Api\\V1\\WebhookRequest',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * FormRequest untuk:
 * - POST /api/v1/webhooks/{provider}
 *
 * Catatan penting:
 * - Verifikasi signature dilakukan oleh middleware VerifyWebhookSignature
 *   sebelum masuk controller.
 * - Middleware itu juga menyimpan raw body ke attribute \'tenrusl_raw_body\'
 *   supaya stream tidak dibaca berkali-kali.
 */',
         'namespace' => 'App\\Http\\Requests\\Api\\V1',
         'uses' => 
        array (
          'formrequest' => 'Illuminate\\Foundation\\Http\\FormRequest',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Foundation\\Http\\FormRequest',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'authorize',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Endpoint webhook adalah public; auth dilakukan via signature (middleware).
     */',
             'namespace' => 'App\\Http\\Requests\\Api\\V1',
             'uses' => 
            array (
              'formrequest' => 'Illuminate\\Foundation\\Http\\FormRequest',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'rawBody',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Ambil raw body dari request.
     *
     * Urutan:
     * 1) Attribute \'tenrusl_raw_body\' yang di-set middleware VerifyWebhookSignature.
     *    Ini jadi sumber utama supaya tidak membaca php://input dua kali.
     * 2) Fallback ke getContent() (Laravel/Symfony biasanya meng-cache body).
     */',
             'namespace' => 'App\\Http\\Requests\\Api\\V1',
             'uses' => 
            array (
              'formrequest' => 'Illuminate\\Foundation\\Http\\FormRequest',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'rules',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Rule validasi untuk field "resmi" (opsional) di webhook.
     *
     * @return array<string, \\Illuminate\\Contracts\\Validation\\ValidationRule|array<mixed>|string>
     */',
             'namespace' => 'App\\Http\\Requests\\Api\\V1',
             'uses' => 
            array (
              'formrequest' => 'Illuminate\\Foundation\\Http\\FormRequest',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Resources\\Api\\V1\\PaymentResource.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Http\\Resources\\Api\\V1\\PaymentResource',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/** @mixin \\App\\Models\\Payment */',
         'namespace' => 'App\\Http\\Resources\\Api\\V1',
         'uses' => 
        array (
          'request' => 'Illuminate\\Http\\Request',
          'jsonresource' => 'Illuminate\\Http\\Resources\\Json\\JsonResource',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Http\\Resources\\Json\\JsonResource',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'toArray',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return array<string, mixed>
     */',
             'namespace' => 'App\\Http\\Resources\\Api\\V1',
             'uses' => 
            array (
              'request' => 'Illuminate\\Http\\Request',
              'jsonresource' => 'Illuminate\\Http\\Resources\\Json\\JsonResource',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'request',
               'type' => 'Illuminate\\Http\\Request',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Http\\Resources\\Api\\V1\\WebhookEventResource.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Http\\Resources\\Api\\V1\\WebhookEventResource',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/** @mixin \\App\\Models\\WebhookEvent */',
         'namespace' => 'App\\Http\\Resources\\Api\\V1',
         'uses' => 
        array (
          'request' => 'Illuminate\\Http\\Request',
          'jsonresource' => 'Illuminate\\Http\\Resources\\Json\\JsonResource',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Http\\Resources\\Json\\JsonResource',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'toArray',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return array<string, mixed>
     */',
             'namespace' => 'App\\Http\\Resources\\Api\\V1',
             'uses' => 
            array (
              'request' => 'Illuminate\\Http\\Request',
              'jsonresource' => 'Illuminate\\Http\\Resources\\Json\\JsonResource',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'request',
               'type' => 'Illuminate\\Http\\Request',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Jobs\\ProcessWebhookEvent.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Jobs\\ProcessWebhookEvent',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Job queue untuk memproses ulang webhook event (jalur retry).
 *
 * Prinsip:
 * - Harus idempotent: kalau job duplicate / dipanggil ulang, tidak boleh bikin state kacau.
 * - Guard di sini mencegah pemrosesan event yang sudah final.
 * - Guard tambahan mencegah job "keduluan" sebelum next_retry_at due (misal delay queue tidak akurat).
 */',
         'namespace' => 'App\\Jobs',
         'uses' => 
        array (
          'webhookevent' => 'App\\Models\\WebhookEvent',
          'webhookprocessor' => 'App\\Services\\Webhooks\\WebhookProcessor',
          'carbonimmutable' => 'Carbon\\CarbonImmutable',
          'carboninterface' => 'Carbon\\CarbonInterface',
          'queueable' => 'Illuminate\\Bus\\Queueable',
          'shouldqueue' => 'Illuminate\\Contracts\\Queue\\ShouldQueue',
          'dispatchable' => 'Illuminate\\Foundation\\Bus\\Dispatchable',
          'interactswithqueue' => 'Illuminate\\Queue\\InteractsWithQueue',
          'serializesmodels' => 'Illuminate\\Queue\\SerializesModels',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => NULL,
       'implements' => 
      array (
        0 => 'Illuminate\\Contracts\\Queue\\ShouldQueue',
      ),
       'usedTraits' => 
      array (
        0 => 'Illuminate\\Foundation\\Bus\\Dispatchable',
        1 => 'Illuminate\\Queue\\InteractsWithQueue',
        2 => 'Illuminate\\Bus\\Queueable',
        3 => 'Illuminate\\Queue\\SerializesModels',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'queue',
          ),
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Queue khusus webhook.
     * Untuk driver database, ini akan mengisi kolom `queue` pada tabel jobs.
     */',
             'namespace' => 'App\\Jobs',
             'uses' => 
            array (
              'webhookevent' => 'App\\Models\\WebhookEvent',
              'webhookprocessor' => 'App\\Services\\Webhooks\\WebhookProcessor',
              'carbonimmutable' => 'Carbon\\CarbonImmutable',
              'carboninterface' => 'Carbon\\CarbonInterface',
              'queueable' => 'Illuminate\\Bus\\Queueable',
              'shouldqueue' => 'Illuminate\\Contracts\\Queue\\ShouldQueue',
              'dispatchable' => 'Illuminate\\Foundation\\Bus\\Dispatchable',
              'interactswithqueue' => 'Illuminate\\Queue\\InteractsWithQueue',
              'serializesmodels' => 'Illuminate\\Queue\\SerializesModels',
            ),
             'constUses' => 
            array (
            ),
          )),
           'type' => 'string',
           'public' => true,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__construct',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'webhookEventId',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'processor',
               'type' => 'App\\Services\\Webhooks\\WebhookProcessor',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Models\\Payment.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Models\\Payment',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Database\\Eloquent\\Model',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
        1 => 'App\\Traits\\HasUlid',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'table',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'incrementing',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => true,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'keyType',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'fillable',
          ),
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Kolom yang boleh diisi mass-assignment.
     * Pastikan ini match dengan migrasi payments kamu.
     */',
             'namespace' => 'App\\Models',
             'uses' => 
            array (
              'hasulid' => 'App\\Traits\\HasUlid',
              'paymentstatus' => 'App\\ValueObjects\\PaymentStatus',
              'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
              'model' => 'Illuminate\\Database\\Eloquent\\Model',
            ),
             'constUses' => 
            array (
            ),
          )),
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'casts',
          ),
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Casting:
     * - JSON meta => array
     * - status => enum/cast PaymentStatus (jika implemented sebagai enum cast) :contentReference[oaicite:5]{index=5}
     */',
             'namespace' => 'App\\Models',
             'uses' => 
            array (
              'hasulid' => 'App\\Traits\\HasUlid',
              'paymentstatus' => 'App\\ValueObjects\\PaymentStatus',
              'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
              'model' => 'Illuminate\\Database\\Eloquent\\Model',
            ),
             'constUses' => 
            array (
            ),
          )),
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        5 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'scopeByProviderRef',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Scope bantu untuk lookup payment berdasarkan provider & provider_ref.
     */',
             'namespace' => 'App\\Models',
             'uses' => 
            array (
              'hasulid' => 'App\\Traits\\HasUlid',
              'paymentstatus' => 'App\\ValueObjects\\PaymentStatus',
              'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
              'model' => 'Illuminate\\Database\\Eloquent\\Model',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'query',
               'type' => NULL,
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'provider',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'providerRef',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Models\\User.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Models\\User',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Foundation\\Auth\\User',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
        1 => 'Illuminate\\Notifications\\Notifiable',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'fillable',
          ),
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Kolom yang boleh diisi mass-assignment.
     */',
             'namespace' => 'App\\Models',
             'uses' => 
            array (
              'mustverifyemail' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
              'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
              'authenticatable' => 'Illuminate\\Foundation\\Auth\\User',
              'notifiable' => 'Illuminate\\Notifications\\Notifiable',
            ),
             'constUses' => 
            array (
            ),
          )),
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'hidden',
          ),
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Kolom yang disembunyikan saat serialisasi.
     */',
             'namespace' => 'App\\Models',
             'uses' => 
            array (
              'mustverifyemail' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
              'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
              'authenticatable' => 'Illuminate\\Foundation\\Auth\\User',
              'notifiable' => 'Illuminate\\Notifications\\Notifiable',
            ),
             'constUses' => 
            array (
            ),
          )),
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'casts',
          ),
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Casting atribut umum Laravel.
     */',
             'namespace' => 'App\\Models',
             'uses' => 
            array (
              'mustverifyemail' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
              'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
              'authenticatable' => 'Illuminate\\Foundation\\Auth\\User',
              'notifiable' => 'Illuminate\\Notifications\\Notifiable',
            ),
             'constUses' => 
            array (
            ),
          )),
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Models\\WebhookEvent.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Models\\WebhookEvent',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Database\\Eloquent\\Model',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
        1 => 'App\\Traits\\HasUlid',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'table',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'incrementing',
          ),
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Karena primary key ULID adalah string.
     * (Biasanya HasUlid juga sudah meng-handle ini, tapi aman dinyatakan eksplisit.)
     */',
             'namespace' => 'App\\Models',
             'uses' => 
            array (
              'hasulid' => 'App\\Traits\\HasUlid',
              'paymentstatus' => 'App\\ValueObjects\\PaymentStatus',
              'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
              'model' => 'Illuminate\\Database\\Eloquent\\Model',
            ),
             'constUses' => 
            array (
            ),
          )),
           'type' => NULL,
           'public' => true,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'keyType',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'guarded',
          ),
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Demo-friendly: izinkan mass assignment untuk semua kolom.
     * Kalau mau lebih ketat, ganti menjadi $fillable.
     */',
             'namespace' => 'App\\Models',
             'uses' => 
            array (
              'hasulid' => 'App\\Traits\\HasUlid',
              'paymentstatus' => 'App\\ValueObjects\\PaymentStatus',
              'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
              'model' => 'Illuminate\\Database\\Eloquent\\Model',
            ),
             'constUses' => 
            array (
            ),
          )),
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'casts',
          ),
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Casting atribut untuk kemudahan pemrosesan.
     * Laravel mendukung enum cast langsung via $casts. :contentReference[oaicite:1]{index=1}
     */',
             'namespace' => 'App\\Models',
             'uses' => 
            array (
              'hasulid' => 'App\\Traits\\HasUlid',
              'paymentstatus' => 'App\\ValueObjects\\PaymentStatus',
              'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
              'model' => 'Illuminate\\Database\\Eloquent\\Model',
            ),
             'constUses' => 
            array (
            ),
          )),
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        5 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'scopeByProviderEvent',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Scope bantu untuk dedup lookup berdasarkan provider & event_id.
     */',
             'namespace' => 'App\\Models',
             'uses' => 
            array (
              'hasulid' => 'App\\Traits\\HasUlid',
              'paymentstatus' => 'App\\ValueObjects\\PaymentStatus',
              'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
              'model' => 'Illuminate\\Database\\Eloquent\\Model',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'query',
               'type' => NULL,
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'provider',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'eventId',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\OpenApi\\Docs.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\OpenApi\\Docs',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * @OA\\Info(
 *   version="1.0.0",
 *   title="TenRusl Payment Webhook Simulator API",
 *   description="Dokumentasi OpenAPI untuk endpoint Payments & Webhooks (simulator)."
 * )
 *
 * @OA\\Server(
 *   url="/",
 *   description="Default App URL"
 * )
 *
 * @OA\\Tag(
 *   name="Payments",
 *   description="Buat dan cek status pembayaran simulasi"
 * )
 * @OA\\Tag(
 *   name="Webhooks",
 *   description="Terima webhook dari berbagai provider (simulasi verifikasi signature)"
 * )
 *
 * @OA\\Schema(
 *   schema="CreatePaymentRequest",
 *   type="object",
 *   required={"provider","amount","currency"},
 *
 *   @OA\\Property(property="provider", type="string", example="xendit"),
 *   @OA\\Property(property="amount", type="integer", example=100000, minimum=1, description="Satuan terkecil (mis. IDR)"),
 *   @OA\\Property(property="currency", type="string", example="IDR", description="Kode ISO-4217 huruf besar (3)"),
 *   @OA\\Property(property="description", type="string", example="Top up"),
 *   @OA\\Property(property="meta", type="object", description="Direkomendasikan; metadata bebas"),
 *   @OA\\Property(property="metadata", type="object", description="Alias yang masih diterima; akan dipetakan ke \'meta\'")
 * )
 *
 * @OA\\Schema(
 *   schema="Payment",
 *   type="object",
 *   required={"provider","provider_ref","status"},
 *
 *   @OA\\Property(property="id", type="string", example="01JCDZQ2F1G8W3X1R7SZM3KZ2S"),
 *   @OA\\Property(property="provider", type="string", example="xendit"),
 *   @OA\\Property(property="provider_ref", type="string", example="sim_xendit_01JCDZQ2F1..."),
 *   @OA\\Property(property="amount", type="integer", example=100000, description="Satuan terkecil"),
 *   @OA\\Property(property="currency", type="string", example="IDR"),
 *   @OA\\Property(property="status", type="string", enum={"pending","succeeded","failed"}, example="pending"),
 *   @OA\\Property(property="meta", type="object"),
 *   @OA\\Property(property="created_at", type="string", format="date-time"),
 *   @OA\\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\\Schema(
 *   schema="WebhookEvent",
 *   type="object",
 *   required={"provider","event_id"},
 *
 *   @OA\\Property(property="id", type="string", example="01JCDZQ5M3..."),
 *   @OA\\Property(property="provider", type="string", example="midtrans"),
 *   @OA\\Property(property="event_id", type="string", example="evt_01JCDZQ5M3..."),
 *   @OA\\Property(property="event_type", type="string", example="invoice.paid", nullable=true),
 *   @OA\\Property(property="payment_provider_ref", type="string", example="sim_midtrans_01J...", nullable=true),
 *   @OA\\Property(property="payment_status", type="string", enum={"pending","succeeded","failed"}, nullable=true),
 *   @OA\\Property(property="attempts", type="integer", example=2),
 *   @OA\\Property(property="received_at", type="string", format="date-time"),
 *   @OA\\Property(property="last_attempt_at", type="string", format="date-time"),
 *   @OA\\Property(property="processed_at", type="string", format="date-time", nullable=true),
 *   @OA\\Property(property="next_retry_at", type="string", format="date-time", nullable=true),
 *   @OA\\Property(property="payload", type="object")
 * )
 *
 * @OA\\Post(
 *   path="/api/v1/payments",
 *   summary="Buat pembayaran simulasi",
 *   tags={"Payments"},
 *
 *   @OA\\RequestBody(
 *     required=true,
 *
 *     @OA\\JsonContent(ref="#/components/schemas/CreatePaymentRequest")
 *   ),
 *
 *   @OA\\Response(
 *     response=201,
 *     description="Created",
 *
 *     @OA\\Header(
 *       header="Idempotency-Key",
 *
 *       @OA\\Schema(type="string"),
 *       description="Idempotency-Key yang dipakai request"
 *     ),
 *
 *     @OA\\JsonContent(
 *
 *       @OA\\Property(property="data", ref="#/components/schemas/Payment")
 *     )
 *   )
 * )
 *
 * @OA\\Get(
 *   path="/api/v1/payments/{provider}/{provider_ref}/status",
 *   summary="Cek status pembayaran",
 *   tags={"Payments"},
 *
 *   @OA\\Parameter(name="provider", in="path", required=true, @OA\\Schema(type="string")),
 *   @OA\\Parameter(name="provider_ref", in="path", required=true, @OA\\Schema(type="string")),
 *
 *   @OA\\Response(
 *     response=200,
 *     description="OK",
 *
 *     @OA\\JsonContent(
 *
 *       @OA\\Property(property="data", ref="#/components/schemas/Payment")
 *     )
 *   )
 * )
 *
 * @OA\\Post(
 *   path="/api/v1/webhooks/{provider}",
 *   summary="Terima webhook dari provider",
 *   tags={"Webhooks"},
 *
 *   @OA\\Parameter(name="provider", in="path", required=true, @OA\\Schema(type="string")),
 *
 *   @OA\\RequestBody(required=true, description="Payload dari provider (JSON atau form)"),
 *
 *   @OA\\Response(
 *     response=202,
 *     description="Accepted (diproses idempotent & retry-aware)",
 *
 *     @OA\\JsonContent(
 *
 *       @OA\\Property(property="data", type="object",
 *         @OA\\Property(property="event", type="object",
 *           @OA\\Property(property="provider", type="string"),
 *           @OA\\Property(property="event_id", type="string"),
 *           @OA\\Property(property="type", type="string", nullable=true)
 *         ),
 *         @OA\\Property(property="result", type="object",
 *           @OA\\Property(property="duplicate", type="boolean"),
 *           @OA\\Property(property="persisted", type="boolean"),
 *           @OA\\Property(property="status", type="string", enum={"pending","succeeded","failed"}),
 *           @OA\\Property(property="payment_provider_ref", type="string", nullable=true),
 *           @OA\\Property(property="next_retry_ms", type="integer", nullable=true)
 *         )
 *       )
 *     )
 *   )
 * )
 */',
         'namespace' => 'App\\OpenApi',
         'uses' => 
        array (
          'oa' => 'OpenApi\\Annotations',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Providers\\AppServiceProvider.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Providers\\AppServiceProvider',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Support\\ServiceProvider',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'register',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Register any application services.
     *
     * Di method ini, lakukan binding ke service container.
     * (Sesuai pedoman Laravel: hanya binding di register; bootstrap di boot)
     */',
             'namespace' => 'App\\Providers',
             'uses' => 
            array (
              'paymentrepository' => 'App\\Repositories\\PaymentRepository',
              'webhookeventrepository' => 'App\\Repositories\\WebhookEventRepository',
              'paymentsservice' => 'App\\Services\\Payments\\PaymentsService',
              'application' => 'Illuminate\\Contracts\\Foundation\\Application',
              'serviceprovider' => 'Illuminate\\Support\\ServiceProvider',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'boot',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Bootstrap any application services.
     */',
             'namespace' => 'App\\Providers',
             'uses' => 
            array (
              'paymentrepository' => 'App\\Repositories\\PaymentRepository',
              'webhookeventrepository' => 'App\\Repositories\\WebhookEventRepository',
              'paymentsservice' => 'App\\Services\\Payments\\PaymentsService',
              'application' => 'Illuminate\\Contracts\\Foundation\\Application',
              'serviceprovider' => 'Illuminate\\Support\\ServiceProvider',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Providers\\RouteServiceProvider.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Providers\\RouteServiceProvider',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Foundation\\Support\\Providers\\RouteServiceProvider',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'boot',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'configureRateLimiting',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Konfigurasi rate limiting untuk web, api, dan webhooks.
     */',
             'namespace' => 'App\\Providers',
             'uses' => 
            array (
              'limit' => 'Illuminate\\Cache\\RateLimiting\\Limit',
              'serviceprovider' => 'Illuminate\\Foundation\\Support\\Providers\\RouteServiceProvider',
              'request' => 'Illuminate\\Http\\Request',
              'ratelimiter' => 'Illuminate\\Support\\Facades\\RateLimiter',
              'route' => 'Illuminate\\Support\\Facades\\Route',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'rateKey',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Key umum rate limiting: default pakai IP.
     */',
             'namespace' => 'App\\Providers',
             'uses' => 
            array (
              'limit' => 'Illuminate\\Cache\\RateLimiting\\Limit',
              'serviceprovider' => 'Illuminate\\Foundation\\Support\\Providers\\RouteServiceProvider',
              'request' => 'Illuminate\\Http\\Request',
              'ratelimiter' => 'Illuminate\\Support\\Facades\\RateLimiter',
              'route' => 'Illuminate\\Support\\Facades\\Route',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'string',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'request',
               'type' => 'Illuminate\\Http\\Request',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Repositories\\PaymentRepository.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Repositories\\PaymentRepository',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'create',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Buat record Payment baru.
     *
     * @param  array{
     *   provider:string,
     *   provider_ref:string,
     *   amount:int|string|float,
     *   currency?:string,
     *   description?:string|null,
     *   meta?:array|null,
     *   status?:string|PaymentStatus|null,
     *   idempotency_key?:string|null,
     *   idempotency_request_hash?:string|null
     * } $attributes
     */',
             'namespace' => 'App\\Repositories',
             'uses' => 
            array (
              'payment' => 'App\\Models\\Payment',
              'paymentstatus' => 'App\\ValueObjects\\PaymentStatus',
              'db' => 'Illuminate\\Support\\Facades\\DB',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'App\\Models\\Payment',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'attributes',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'find',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => '?App\\Models\\Payment',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'id',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'findByProviderRef',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => '?App\\Models\\Payment',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'provider',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'providerRef',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'findByIdempotencyKey',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * (Opsional tapi berguna untuk flow idempotency)
     */',
             'namespace' => 'App\\Repositories',
             'uses' => 
            array (
              'payment' => 'App\\Models\\Payment',
              'paymentstatus' => 'App\\ValueObjects\\PaymentStatus',
              'db' => 'Illuminate\\Support\\Facades\\DB',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => '?App\\Models\\Payment',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'key',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'updateStatusByProviderRef',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Update status payment berdasarkan (provider, provider_ref).
     *
     * Aturan “state transition” yang aman:
     * - Payment final (succeeded/failed) TIDAK boleh balik ke pending.
     * - Payment final juga tidak kita "flip" ke final lain (succeeded <-> failed).
     *   (First final wins; repeated same final is OK / idempotent.)
     *
     * Return: jumlah row yang ter-update.
     */',
             'namespace' => 'App\\Repositories',
             'uses' => 
            array (
              'payment' => 'App\\Models\\Payment',
              'paymentstatus' => 'App\\ValueObjects\\PaymentStatus',
              'db' => 'Illuminate\\Support\\Facades\\DB',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'provider',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'providerRef',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'status',
               'type' => 'string|App\\ValueObjects\\PaymentStatus',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        5 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'save',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'payment',
               'type' => 'App\\Models\\Payment',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        6 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'delete',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'id',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        7 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'massUpdate',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Operasi update via Query Builder (kalau perlu hemat memori).
     */',
             'namespace' => 'App\\Repositories',
             'uses' => 
            array (
              'payment' => 'App\\Models\\Payment',
              'paymentstatus' => 'App\\ValueObjects\\PaymentStatus',
              'db' => 'Illuminate\\Support\\Facades\\DB',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'where',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'payload',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Repositories\\WebhookEventRepository.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Repositories\\WebhookEventRepository',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'findByProviderEvent',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Ambil event berdasarkan dedup key (provider,event_id).
     *
     * Catatan:
     * - Jika $forUpdate=true, baris akan di-lock (pessimistic lock).
     * - Lock ini hanya efektif jika dipanggil di dalam DB transaction.
     */',
             'namespace' => 'App\\Repositories',
             'uses' => 
            array (
              'webhookevent' => 'App\\Models\\WebhookEvent',
              'paymentstatus' => 'App\\ValueObjects\\PaymentStatus',
              'datetimeinterface' => 'DateTimeInterface',
              'queryexception' => 'Illuminate\\Database\\QueryException',
              'carbon' => 'Illuminate\\Support\\Carbon',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => '?App\\Models\\WebhookEvent',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'provider',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'eventId',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'forUpdate',
               'type' => 'bool',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'storeNewOrGetExisting',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Insert event baru, atau kalau duplicate key (unique provider+event_id) maka ambil event existing.
     *
     * Return: [WebhookEvent $event, bool $duplicate]
     */',
             'namespace' => 'App\\Repositories',
             'uses' => 
            array (
              'webhookevent' => 'App\\Models\\WebhookEvent',
              'paymentstatus' => 'App\\ValueObjects\\PaymentStatus',
              'datetimeinterface' => 'DateTimeInterface',
              'queryexception' => 'Illuminate\\Database\\QueryException',
              'carbon' => 'Illuminate\\Support\\Carbon',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'provider',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'eventId',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'eventType',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            3 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'rawBody',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            4 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'payload',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            5 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'receivedAt',
               'type' => '?DateTimeInterface',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
            )),
            6 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'lockExisting',
               'type' => 'bool',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'touchAttempt',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Tambah attempts & catat waktu attempt terakhir.
     */',
             'namespace' => 'App\\Repositories',
             'uses' => 
            array (
              'webhookevent' => 'App\\Models\\WebhookEvent',
              'paymentstatus' => 'App\\ValueObjects\\PaymentStatus',
              'datetimeinterface' => 'DateTimeInterface',
              'queryexception' => 'Illuminate\\Database\\QueryException',
              'carbon' => 'Illuminate\\Support\\Carbon',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'event',
               'type' => 'App\\Models\\WebhookEvent',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'at',
               'type' => '?DateTimeInterface',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'markProcessed',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Tandai event sukses diproses.
     */',
             'namespace' => 'App\\Repositories',
             'uses' => 
            array (
              'webhookevent' => 'App\\Models\\WebhookEvent',
              'paymentstatus' => 'App\\ValueObjects\\PaymentStatus',
              'datetimeinterface' => 'DateTimeInterface',
              'queryexception' => 'Illuminate\\Database\\QueryException',
              'carbon' => 'Illuminate\\Support\\Carbon',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'event',
               'type' => 'App\\Models\\WebhookEvent',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'paymentProviderRef',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'status',
               'type' => 'App\\ValueObjects\\PaymentStatus|string|null',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            3 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'processedAt',
               'type' => '?DateTimeInterface',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'markFailed',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Tandai event gagal diproses.
     */',
             'namespace' => 'App\\Repositories',
             'uses' => 
            array (
              'webhookevent' => 'App\\Models\\WebhookEvent',
              'paymentstatus' => 'App\\ValueObjects\\PaymentStatus',
              'datetimeinterface' => 'DateTimeInterface',
              'queryexception' => 'Illuminate\\Database\\QueryException',
              'carbon' => 'Illuminate\\Support\\Carbon',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'event',
               'type' => 'App\\Models\\WebhookEvent',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'message',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'at',
               'type' => '?DateTimeInterface',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        5 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'scheduleNextRetry',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Jadwalkan retry berikutnya.
     */',
             'namespace' => 'App\\Repositories',
             'uses' => 
            array (
              'webhookevent' => 'App\\Models\\WebhookEvent',
              'paymentstatus' => 'App\\ValueObjects\\PaymentStatus',
              'datetimeinterface' => 'DateTimeInterface',
              'queryexception' => 'Illuminate\\Database\\QueryException',
              'carbon' => 'Illuminate\\Support\\Carbon',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'event',
               'type' => 'App\\Models\\WebhookEvent',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'nextAt',
               'type' => 'DateTimeInterface',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'message',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Idempotency\\IdempotencyKeyService.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Services\\Idempotency\\IdempotencyKeyService',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__construct',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'fingerprint',
               'type' => 'App\\Services\\Idempotency\\RequestFingerprint',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'resolveKey',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'request',
               'type' => 'Illuminate\\Http\\Request',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'acquireLock',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'key',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'releaseLock',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'key',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'storeResponse',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Simpan response pertama untuk key (untuk replay idempotent).
     *
     * Dibuat fleksibel: headers/body boleh tidak ada (sesuai pemakaian `??`),
     * ini yang menghilangkan error PHPStan "offset headers ... ?? always exists".
     *
     * @param array{
     *   status:int,
     *   headers?:array<string,string|string[]>,
     *   body?:mixed
     * } $response
     */',
             'namespace' => 'App\\Services\\Idempotency',
             'uses' => 
            array (
              'lock' => 'Illuminate\\Contracts\\Cache\\Lock',
              'lockprovider' => 'Illuminate\\Contracts\\Cache\\LockProvider',
              'request' => 'Illuminate\\Http\\Request',
              'arr' => 'Illuminate\\Support\\Arr',
              'cache' => 'Illuminate\\Support\\Facades\\Cache',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'key',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'response',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        5 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'getStoredResponse',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return array{
     *   status:int,
     *   headers:array<string,string|string[]>,
     *   body:mixed
     * }|null
     */',
             'namespace' => 'App\\Services\\Idempotency',
             'uses' => 
            array (
              'lock' => 'Illuminate\\Contracts\\Cache\\Lock',
              'lockprovider' => 'Illuminate\\Contracts\\Cache\\LockProvider',
              'request' => 'Illuminate\\Http\\Request',
              'arr' => 'Illuminate\\Support\\Arr',
              'cache' => 'Illuminate\\Support\\Facades\\Cache',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => '?array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'key',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Idempotency\\RequestFingerprint.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Services\\Idempotency\\RequestFingerprint',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Membuat fingerprint stabil dari isi request (method+path+headers penting+payload).
 * Dipakai saat client tidak mengirim "Idempotency-Key".
 */',
         'namespace' => 'App\\Services\\Idempotency',
         'uses' => 
        array (
          'request' => 'Illuminate\\Http\\Request',
          'str' => 'Illuminate\\Support\\Str',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'hash',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Hasilkan hash hex (sha256) dari representasi kanonik request.
     */',
             'namespace' => 'App\\Services\\Idempotency',
             'uses' => 
            array (
              'request' => 'Illuminate\\Http\\Request',
              'str' => 'Illuminate\\Support\\Str',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'request',
               'type' => 'Illuminate\\Http\\Request',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'canonical',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Bentuk string kanonik yang stabil:
     *   METHOD \\n PATH \\n content-type \\n accept \\n body-json-terurut|raw
     */',
             'namespace' => 'App\\Services\\Idempotency',
             'uses' => 
            array (
              'request' => 'Illuminate\\Http\\Request',
              'str' => 'Illuminate\\Support\\Str',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'request',
               'type' => 'Illuminate\\Http\\Request',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\AirwallexAdapter.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Services\\Payments\\Adapters\\AirwallexAdapter',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
        0 => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'provider',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'create',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @param  array{amount:int|string, currency?:string, description?:string, metadata?:array}  $input
     * @return array{provider:string, provider_ref:string, status:string, snapshot:array}
     */',
             'namespace' => 'App\\Services\\Payments\\Adapters',
             'uses' => 
            array (
              'paymentadapter' => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
              'str' => 'Illuminate\\Support\\Str',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'input',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'status',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return array{provider:string, provider_ref:string, status:string}
     */',
             'namespace' => 'App\\Services\\Payments\\Adapters',
             'uses' => 
            array (
              'paymentadapter' => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
              'str' => 'Illuminate\\Support\\Str',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'providerRef',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\AmazonBwpAdapter.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Services\\Payments\\Adapters\\AmazonBwpAdapter',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
        0 => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'provider',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'create',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @param  array{amount:int|string, currency?:string, description?:string, metadata?:array}  $input
     * @return array{provider:string, provider_ref:string, status:string, snapshot:array}
     */',
             'namespace' => 'App\\Services\\Payments\\Adapters',
             'uses' => 
            array (
              'paymentadapter' => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
              'str' => 'Illuminate\\Support\\Str',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'input',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'status',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return array{provider:string, provider_ref:string, status:string}
     */',
             'namespace' => 'App\\Services\\Payments\\Adapters',
             'uses' => 
            array (
              'paymentadapter' => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
              'str' => 'Illuminate\\Support\\Str',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'providerRef',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\DanaAdapter.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Services\\Payments\\Adapters\\DanaAdapter',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
        0 => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'provider',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'create',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @param  array{amount:int|string, currency?:string, description?:string, metadata?:array}  $input
     * @return array{provider:string, provider_ref:string, status:string, snapshot:array}
     */',
             'namespace' => 'App\\Services\\Payments\\Adapters',
             'uses' => 
            array (
              'paymentadapter' => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
              'str' => 'Illuminate\\Support\\Str',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'input',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'status',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return array{provider:string, provider_ref:string, status:string}
     */',
             'namespace' => 'App\\Services\\Payments\\Adapters',
             'uses' => 
            array (
              'paymentadapter' => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
              'str' => 'Illuminate\\Support\\Str',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'providerRef',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\DokuAdapter.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Services\\Payments\\Adapters\\DokuAdapter',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
        0 => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'provider',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'create',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @param  array{amount:int|string, currency?:string, description?:string, metadata?:array}  $input
     * @return array{provider:string, provider_ref:string, status:string, snapshot:array}
     */',
             'namespace' => 'App\\Services\\Payments\\Adapters',
             'uses' => 
            array (
              'paymentadapter' => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
              'str' => 'Illuminate\\Support\\Str',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'input',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'status',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return array{provider:string, provider_ref:string, status:string}
     */',
             'namespace' => 'App\\Services\\Payments\\Adapters',
             'uses' => 
            array (
              'paymentadapter' => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
              'str' => 'Illuminate\\Support\\Str',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'providerRef',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\LemonSqueezyAdapter.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Services\\Payments\\Adapters\\LemonSqueezyAdapter',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
        0 => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'provider',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'create',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @param  array{amount:int|string, currency?:string, description?:string, metadata?:array}  $input
     * @return array{provider:string, provider_ref:string, status:string, snapshot:array}
     */',
             'namespace' => 'App\\Services\\Payments\\Adapters',
             'uses' => 
            array (
              'paymentadapter' => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
              'str' => 'Illuminate\\Support\\Str',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'input',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'status',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return array{provider:string, provider_ref:string, status:string}
     */',
             'namespace' => 'App\\Services\\Payments\\Adapters',
             'uses' => 
            array (
              'paymentadapter' => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
              'str' => 'Illuminate\\Support\\Str',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'providerRef',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\MidtransAdapter.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Services\\Payments\\Adapters\\MidtransAdapter',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
        0 => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'provider',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'create',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Simulator "create payment" untuk Midtrans.
     * Tidak memanggil API Midtrans sungguhan—hanya membuat reference & snapshot.
     *
     * @param  array{amount:int|string, currency?:string, description?:string, metadata?:array}  $input
     * @return array{provider:string, provider_ref:string, status:string, snapshot:array}
     */',
             'namespace' => 'App\\Services\\Payments\\Adapters',
             'uses' => 
            array (
              'paymentadapter' => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
              'str' => 'Illuminate\\Support\\Str',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'input',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'status',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Status sederhana (simulasi).
     * Pada implementasi nyata, status ditentukan oleh notifikasi webhook Midtrans.
     *
     * @return array{provider:string, provider_ref:string, status:string}
     */',
             'namespace' => 'App\\Services\\Payments\\Adapters',
             'uses' => 
            array (
              'paymentadapter' => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
              'str' => 'Illuminate\\Support\\Str',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'providerRef',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\MockAdapter.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Services\\Payments\\Adapters\\MockAdapter',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
        0 => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'provider',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'create',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Adapter dummy untuk keperluan demo/idempotency flow.
     *
     * @param  array{amount:int|string, currency?:string, description?:string, metadata?:array}  $input
     * @return array{provider:string, provider_ref:string, status:string, snapshot:array}
     */',
             'namespace' => 'App\\Services\\Payments\\Adapters',
             'uses' => 
            array (
              'paymentadapter' => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
              'str' => 'Illuminate\\Support\\Str',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'input',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'status',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return array{provider:string, provider_ref:string, status:string}
     */',
             'namespace' => 'App\\Services\\Payments\\Adapters',
             'uses' => 
            array (
              'paymentadapter' => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
              'str' => 'Illuminate\\Support\\Str',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'providerRef',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\OyAdapter.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Services\\Payments\\Adapters\\OyAdapter',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
        0 => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'provider',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'create',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @param  array{amount:int|string, currency?:string, description?:string, metadata?:array}  $input
     * @return array{provider:string, provider_ref:string, status:string, snapshot:array}
     */',
             'namespace' => 'App\\Services\\Payments\\Adapters',
             'uses' => 
            array (
              'paymentadapter' => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
              'str' => 'Illuminate\\Support\\Str',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'input',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'status',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return array{provider:string, provider_ref:string, status:string}
     */',
             'namespace' => 'App\\Services\\Payments\\Adapters',
             'uses' => 
            array (
              'paymentadapter' => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
              'str' => 'Illuminate\\Support\\Str',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'providerRef',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\PaddleAdapter.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Services\\Payments\\Adapters\\PaddleAdapter',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
        0 => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'provider',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'create',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @param  array{amount:int|string, currency?:string, description?:string, metadata?:array}  $input
     * @return array{provider:string, provider_ref:string, status:string, snapshot:array}
     */',
             'namespace' => 'App\\Services\\Payments\\Adapters',
             'uses' => 
            array (
              'paymentadapter' => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
              'str' => 'Illuminate\\Support\\Str',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'input',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'status',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return array{provider:string, provider_ref:string, status:string}
     */',
             'namespace' => 'App\\Services\\Payments\\Adapters',
             'uses' => 
            array (
              'paymentadapter' => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
              'str' => 'Illuminate\\Support\\Str',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'providerRef',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\PayoneerAdapter.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Services\\Payments\\Adapters\\PayoneerAdapter',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
        0 => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'provider',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'create',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @param  array{amount:int|string, currency?:string, description?:string, metadata?:array}  $input
     * @return array{provider:string, provider_ref:string, status:string, snapshot:array}
     */',
             'namespace' => 'App\\Services\\Payments\\Adapters',
             'uses' => 
            array (
              'paymentadapter' => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
              'str' => 'Illuminate\\Support\\Str',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'input',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'status',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return array{provider:string, provider_ref:string, status:string}
     */',
             'namespace' => 'App\\Services\\Payments\\Adapters',
             'uses' => 
            array (
              'paymentadapter' => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
              'str' => 'Illuminate\\Support\\Str',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'providerRef',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\PaypalAdapter.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Services\\Payments\\Adapters\\PaypalAdapter',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
        0 => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'provider',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'create',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @param  array{amount:int|string, currency?:string, description?:string, metadata?:array}  $input
     * @return array{provider:string, provider_ref:string, status:string, snapshot:array}
     */',
             'namespace' => 'App\\Services\\Payments\\Adapters',
             'uses' => 
            array (
              'paymentadapter' => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
              'str' => 'Illuminate\\Support\\Str',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'input',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'status',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return array{provider:string, provider_ref:string, status:string}
     */',
             'namespace' => 'App\\Services\\Payments\\Adapters',
             'uses' => 
            array (
              'paymentadapter' => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
              'str' => 'Illuminate\\Support\\Str',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'providerRef',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\SkrillAdapter.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Services\\Payments\\Adapters\\SkrillAdapter',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
        0 => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'provider',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'create',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @param  array{amount:int|string, currency?:string, description?:string, metadata?:array}  $input
     * @return array{provider:string, provider_ref:string, status:string, snapshot:array}
     */',
             'namespace' => 'App\\Services\\Payments\\Adapters',
             'uses' => 
            array (
              'paymentadapter' => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
              'str' => 'Illuminate\\Support\\Str',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'input',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'status',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return array{provider:string, provider_ref:string, status:string}
     */',
             'namespace' => 'App\\Services\\Payments\\Adapters',
             'uses' => 
            array (
              'paymentadapter' => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
              'str' => 'Illuminate\\Support\\Str',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'providerRef',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\StripeAdapter.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Services\\Payments\\Adapters\\StripeAdapter',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
        0 => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'provider',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Nama provider (harus konsisten dengan routes & allowlist).
     */',
             'namespace' => 'App\\Services\\Payments\\Adapters',
             'uses' => 
            array (
              'paymentadapter' => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
              'str' => 'Illuminate\\Support\\Str',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'create',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Simulator "create payment".
     * Tidak memanggil API Stripe sungguhan — hanya membuat reference & snapshot.
     *
     * @param  array{amount:int|string, currency?:string, description?:string, metadata?:array}  $input
     * @return array{provider:string, provider_ref:string, status:string, snapshot:array}
     */',
             'namespace' => 'App\\Services\\Payments\\Adapters',
             'uses' => 
            array (
              'paymentadapter' => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
              'str' => 'Illuminate\\Support\\Str',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'input',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'status',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Simulator "get status".
     * Pada demo ini status dibuat sederhana (pending → menunggu webhook).
     *
     * @return array{provider:string, provider_ref:string, status:string}
     */',
             'namespace' => 'App\\Services\\Payments\\Adapters',
             'uses' => 
            array (
              'paymentadapter' => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
              'str' => 'Illuminate\\Support\\Str',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'providerRef',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\TripayAdapter.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Services\\Payments\\Adapters\\TripayAdapter',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
        0 => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'provider',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'create',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @param  array{amount:int|string, currency?:string, description?:string, metadata?:array}  $input
     * @return array{provider:string, provider_ref:string, status:string, snapshot:array}
     */',
             'namespace' => 'App\\Services\\Payments\\Adapters',
             'uses' => 
            array (
              'paymentadapter' => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
              'str' => 'Illuminate\\Support\\Str',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'input',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'status',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return array{provider:string, provider_ref:string, status:string}
     */',
             'namespace' => 'App\\Services\\Payments\\Adapters',
             'uses' => 
            array (
              'paymentadapter' => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
              'str' => 'Illuminate\\Support\\Str',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'providerRef',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Adapters\\XenditAdapter.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Services\\Payments\\Adapters\\XenditAdapter',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
        0 => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'provider',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'create',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Simulator "create payment" untuk Xendit.
     * Tidak memanggil API Xendit; status final menunggu webhook (x-callback-token).
     *
     * @param  array{amount:int|string, currency?:string, description?:string, metadata?:array}  $input
     * @return array{provider:string, provider_ref:string, status:string, snapshot:array}
     */',
             'namespace' => 'App\\Services\\Payments\\Adapters',
             'uses' => 
            array (
              'paymentadapter' => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
              'str' => 'Illuminate\\Support\\Str',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'input',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'status',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return array{provider:string, provider_ref:string, status:string}
     */',
             'namespace' => 'App\\Services\\Payments\\Adapters',
             'uses' => 
            array (
              'paymentadapter' => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
              'str' => 'Illuminate\\Support\\Str',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'providerRef',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\Contracts\\PaymentAdapter.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedInterfaceNode::__set_state(array(
       'name' => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Kontrak umum untuk adapter pembayaran di simulator.
 *
 * Setiap adapter harus:
 * - Mengembalikan nama provider (harus selaras dengan allowlist & route param).
 * - Menyediakan operasi create() (membuat referensi pembayaran simulasi).
 * - Menyediakan operasi status() (mengambil status berdasarkan provider_ref).
 */',
         'namespace' => 'App\\Services\\Payments\\Contracts',
         'uses' => 
        array (
        ),
         'constUses' => 
        array (
        ),
      )),
       'extends' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'provider',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Nama provider, contoh: "mock", "xendit", "midtrans", "stripe", dll.
     */',
             'namespace' => 'App\\Services\\Payments\\Contracts',
             'uses' => 
            array (
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'create',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Buat pembayaran simulasi.
     *
     * @param  array  $input  bebas (amount/currency/description/metadata, dsb.)
     * @return array{provider:string, provider_ref:string, status:string, snapshot:array}
     */',
             'namespace' => 'App\\Services\\Payments\\Contracts',
             'uses' => 
            array (
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'input',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'status',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Ambil status simulasi berdasarkan provider_ref.
     *
     * @return array{provider:string, provider_ref:string, status:string}
     */',
             'namespace' => 'App\\Services\\Payments\\Contracts',
             'uses' => 
            array (
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'providerRef',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Payments\\PaymentsService.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Services\\Payments\\PaymentsService',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Registry sederhana untuk adapter pembayaran + operasi create/status.
 *
 * Catatan:
 * - Secara default akan mendaftarkan semua adapter bawaan.
 * - Jika ingin meng-overwrite, injeksikan array $adapters via container.
 * - Penyaringan berdasarkan allowlist: config(\'tenrusl.providers_allowlist\').
 */',
         'namespace' => 'App\\Services\\Payments',
         'uses' => 
        array (
          'airwallexadapter' => 'App\\Services\\Payments\\Adapters\\AirwallexAdapter',
          'amazonbwpadapter' => 'App\\Services\\Payments\\Adapters\\AmazonBwpAdapter',
          'danaadapter' => 'App\\Services\\Payments\\Adapters\\DanaAdapter',
          'dokuadapter' => 'App\\Services\\Payments\\Adapters\\DokuAdapter',
          'lemonsqueezyadapter' => 'App\\Services\\Payments\\Adapters\\LemonSqueezyAdapter',
          'midtransadapter' => 'App\\Services\\Payments\\Adapters\\MidtransAdapter',
          'mockadapter' => 'App\\Services\\Payments\\Adapters\\MockAdapter',
          'oyadapter' => 'App\\Services\\Payments\\Adapters\\OyAdapter',
          'paddleadapter' => 'App\\Services\\Payments\\Adapters\\PaddleAdapter',
          'payoneeradapter' => 'App\\Services\\Payments\\Adapters\\PayoneerAdapter',
          'paypaladapter' => 'App\\Services\\Payments\\Adapters\\PaypalAdapter',
          'skrilladapter' => 'App\\Services\\Payments\\Adapters\\SkrillAdapter',
          'stripeadapter' => 'App\\Services\\Payments\\Adapters\\StripeAdapter',
          'tripayadapter' => 'App\\Services\\Payments\\Adapters\\TripayAdapter',
          'xenditadapter' => 'App\\Services\\Payments\\Adapters\\XenditAdapter',
          'paymentadapter' => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
          'invalidargumentexception' => 'InvalidArgumentException',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__construct',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @param  iterable<PaymentAdapter>|null  $adapters
     * @param  array<string>|null  $allowedProviders
     */',
             'namespace' => 'App\\Services\\Payments',
             'uses' => 
            array (
              'airwallexadapter' => 'App\\Services\\Payments\\Adapters\\AirwallexAdapter',
              'amazonbwpadapter' => 'App\\Services\\Payments\\Adapters\\AmazonBwpAdapter',
              'danaadapter' => 'App\\Services\\Payments\\Adapters\\DanaAdapter',
              'dokuadapter' => 'App\\Services\\Payments\\Adapters\\DokuAdapter',
              'lemonsqueezyadapter' => 'App\\Services\\Payments\\Adapters\\LemonSqueezyAdapter',
              'midtransadapter' => 'App\\Services\\Payments\\Adapters\\MidtransAdapter',
              'mockadapter' => 'App\\Services\\Payments\\Adapters\\MockAdapter',
              'oyadapter' => 'App\\Services\\Payments\\Adapters\\OyAdapter',
              'paddleadapter' => 'App\\Services\\Payments\\Adapters\\PaddleAdapter',
              'payoneeradapter' => 'App\\Services\\Payments\\Adapters\\PayoneerAdapter',
              'paypaladapter' => 'App\\Services\\Payments\\Adapters\\PaypalAdapter',
              'skrilladapter' => 'App\\Services\\Payments\\Adapters\\SkrillAdapter',
              'stripeadapter' => 'App\\Services\\Payments\\Adapters\\StripeAdapter',
              'tripayadapter' => 'App\\Services\\Payments\\Adapters\\TripayAdapter',
              'xenditadapter' => 'App\\Services\\Payments\\Adapters\\XenditAdapter',
              'paymentadapter' => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
              'invalidargumentexception' => 'InvalidArgumentException',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'adapters',
               'type' => '?iterable',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'allowedProviders',
               'type' => '?array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'addAdapter',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Tambahkan adapter baru secara dinamis.
     */',
             'namespace' => 'App\\Services\\Payments',
             'uses' => 
            array (
              'airwallexadapter' => 'App\\Services\\Payments\\Adapters\\AirwallexAdapter',
              'amazonbwpadapter' => 'App\\Services\\Payments\\Adapters\\AmazonBwpAdapter',
              'danaadapter' => 'App\\Services\\Payments\\Adapters\\DanaAdapter',
              'dokuadapter' => 'App\\Services\\Payments\\Adapters\\DokuAdapter',
              'lemonsqueezyadapter' => 'App\\Services\\Payments\\Adapters\\LemonSqueezyAdapter',
              'midtransadapter' => 'App\\Services\\Payments\\Adapters\\MidtransAdapter',
              'mockadapter' => 'App\\Services\\Payments\\Adapters\\MockAdapter',
              'oyadapter' => 'App\\Services\\Payments\\Adapters\\OyAdapter',
              'paddleadapter' => 'App\\Services\\Payments\\Adapters\\PaddleAdapter',
              'payoneeradapter' => 'App\\Services\\Payments\\Adapters\\PayoneerAdapter',
              'paypaladapter' => 'App\\Services\\Payments\\Adapters\\PaypalAdapter',
              'skrilladapter' => 'App\\Services\\Payments\\Adapters\\SkrillAdapter',
              'stripeadapter' => 'App\\Services\\Payments\\Adapters\\StripeAdapter',
              'tripayadapter' => 'App\\Services\\Payments\\Adapters\\TripayAdapter',
              'xenditadapter' => 'App\\Services\\Payments\\Adapters\\XenditAdapter',
              'paymentadapter' => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
              'invalidargumentexception' => 'InvalidArgumentException',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'adapter',
               'type' => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'providers',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Dapatkan daftar provider yang tersedia.
     *
     * @return string[]
     */',
             'namespace' => 'App\\Services\\Payments',
             'uses' => 
            array (
              'airwallexadapter' => 'App\\Services\\Payments\\Adapters\\AirwallexAdapter',
              'amazonbwpadapter' => 'App\\Services\\Payments\\Adapters\\AmazonBwpAdapter',
              'danaadapter' => 'App\\Services\\Payments\\Adapters\\DanaAdapter',
              'dokuadapter' => 'App\\Services\\Payments\\Adapters\\DokuAdapter',
              'lemonsqueezyadapter' => 'App\\Services\\Payments\\Adapters\\LemonSqueezyAdapter',
              'midtransadapter' => 'App\\Services\\Payments\\Adapters\\MidtransAdapter',
              'mockadapter' => 'App\\Services\\Payments\\Adapters\\MockAdapter',
              'oyadapter' => 'App\\Services\\Payments\\Adapters\\OyAdapter',
              'paddleadapter' => 'App\\Services\\Payments\\Adapters\\PaddleAdapter',
              'payoneeradapter' => 'App\\Services\\Payments\\Adapters\\PayoneerAdapter',
              'paypaladapter' => 'App\\Services\\Payments\\Adapters\\PaypalAdapter',
              'skrilladapter' => 'App\\Services\\Payments\\Adapters\\SkrillAdapter',
              'stripeadapter' => 'App\\Services\\Payments\\Adapters\\StripeAdapter',
              'tripayadapter' => 'App\\Services\\Payments\\Adapters\\TripayAdapter',
              'xenditadapter' => 'App\\Services\\Payments\\Adapters\\XenditAdapter',
              'paymentadapter' => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
              'invalidargumentexception' => 'InvalidArgumentException',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'getAdapter',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Ambil adapter untuk provider tertentu.
     */',
             'namespace' => 'App\\Services\\Payments',
             'uses' => 
            array (
              'airwallexadapter' => 'App\\Services\\Payments\\Adapters\\AirwallexAdapter',
              'amazonbwpadapter' => 'App\\Services\\Payments\\Adapters\\AmazonBwpAdapter',
              'danaadapter' => 'App\\Services\\Payments\\Adapters\\DanaAdapter',
              'dokuadapter' => 'App\\Services\\Payments\\Adapters\\DokuAdapter',
              'lemonsqueezyadapter' => 'App\\Services\\Payments\\Adapters\\LemonSqueezyAdapter',
              'midtransadapter' => 'App\\Services\\Payments\\Adapters\\MidtransAdapter',
              'mockadapter' => 'App\\Services\\Payments\\Adapters\\MockAdapter',
              'oyadapter' => 'App\\Services\\Payments\\Adapters\\OyAdapter',
              'paddleadapter' => 'App\\Services\\Payments\\Adapters\\PaddleAdapter',
              'payoneeradapter' => 'App\\Services\\Payments\\Adapters\\PayoneerAdapter',
              'paypaladapter' => 'App\\Services\\Payments\\Adapters\\PaypalAdapter',
              'skrilladapter' => 'App\\Services\\Payments\\Adapters\\SkrillAdapter',
              'stripeadapter' => 'App\\Services\\Payments\\Adapters\\StripeAdapter',
              'tripayadapter' => 'App\\Services\\Payments\\Adapters\\TripayAdapter',
              'xenditadapter' => 'App\\Services\\Payments\\Adapters\\XenditAdapter',
              'paymentadapter' => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
              'invalidargumentexception' => 'InvalidArgumentException',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'provider',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'create',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Buat pembayaran simulasi via adapter terkait.
     *
     * @return array{provider:string, provider_ref:string, status:string, snapshot:array}
     */',
             'namespace' => 'App\\Services\\Payments',
             'uses' => 
            array (
              'airwallexadapter' => 'App\\Services\\Payments\\Adapters\\AirwallexAdapter',
              'amazonbwpadapter' => 'App\\Services\\Payments\\Adapters\\AmazonBwpAdapter',
              'danaadapter' => 'App\\Services\\Payments\\Adapters\\DanaAdapter',
              'dokuadapter' => 'App\\Services\\Payments\\Adapters\\DokuAdapter',
              'lemonsqueezyadapter' => 'App\\Services\\Payments\\Adapters\\LemonSqueezyAdapter',
              'midtransadapter' => 'App\\Services\\Payments\\Adapters\\MidtransAdapter',
              'mockadapter' => 'App\\Services\\Payments\\Adapters\\MockAdapter',
              'oyadapter' => 'App\\Services\\Payments\\Adapters\\OyAdapter',
              'paddleadapter' => 'App\\Services\\Payments\\Adapters\\PaddleAdapter',
              'payoneeradapter' => 'App\\Services\\Payments\\Adapters\\PayoneerAdapter',
              'paypaladapter' => 'App\\Services\\Payments\\Adapters\\PaypalAdapter',
              'skrilladapter' => 'App\\Services\\Payments\\Adapters\\SkrillAdapter',
              'stripeadapter' => 'App\\Services\\Payments\\Adapters\\StripeAdapter',
              'tripayadapter' => 'App\\Services\\Payments\\Adapters\\TripayAdapter',
              'xenditadapter' => 'App\\Services\\Payments\\Adapters\\XenditAdapter',
              'paymentadapter' => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
              'invalidargumentexception' => 'InvalidArgumentException',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'provider',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'input',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        5 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'status',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Ambil status simulasi via adapter terkait.
     *
     * @return array{provider:string, provider_ref:string, status:string}
     */',
             'namespace' => 'App\\Services\\Payments',
             'uses' => 
            array (
              'airwallexadapter' => 'App\\Services\\Payments\\Adapters\\AirwallexAdapter',
              'amazonbwpadapter' => 'App\\Services\\Payments\\Adapters\\AmazonBwpAdapter',
              'danaadapter' => 'App\\Services\\Payments\\Adapters\\DanaAdapter',
              'dokuadapter' => 'App\\Services\\Payments\\Adapters\\DokuAdapter',
              'lemonsqueezyadapter' => 'App\\Services\\Payments\\Adapters\\LemonSqueezyAdapter',
              'midtransadapter' => 'App\\Services\\Payments\\Adapters\\MidtransAdapter',
              'mockadapter' => 'App\\Services\\Payments\\Adapters\\MockAdapter',
              'oyadapter' => 'App\\Services\\Payments\\Adapters\\OyAdapter',
              'paddleadapter' => 'App\\Services\\Payments\\Adapters\\PaddleAdapter',
              'payoneeradapter' => 'App\\Services\\Payments\\Adapters\\PayoneerAdapter',
              'paypaladapter' => 'App\\Services\\Payments\\Adapters\\PaypalAdapter',
              'skrilladapter' => 'App\\Services\\Payments\\Adapters\\SkrillAdapter',
              'stripeadapter' => 'App\\Services\\Payments\\Adapters\\StripeAdapter',
              'tripayadapter' => 'App\\Services\\Payments\\Adapters\\TripayAdapter',
              'xenditadapter' => 'App\\Services\\Payments\\Adapters\\XenditAdapter',
              'paymentadapter' => 'App\\Services\\Payments\\Contracts\\PaymentAdapter',
              'invalidargumentexception' => 'InvalidArgumentException',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'provider',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'providerRef',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Signatures\\AirwallexSignature.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Services\\Signatures\\AirwallexSignature',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'verify',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Verify Airwallex webhook signature.
     *
     * Docs (umum): x-timestamp + x-signature (HMAC-SHA256)
     * value_to_digest = "{$timestamp}{$rawBody}"
     * signature = hex(HMAC_SHA256(secret, value_to_digest))
     */',
             'namespace' => 'App\\Services\\Signatures',
             'uses' => 
            array (
              'request' => 'Illuminate\\Http\\Request',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'rawBody',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'request',
               'type' => 'Illuminate\\Http\\Request',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Signatures\\AmazonBwpSignature.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Services\\Signatures\\AmazonBwpSignature',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'verify',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Verify Amazon Buy with Prime webhook signature (SIMULATOR).
     *
     * Header:
     *  - x-amzn-signature : base64 ECDSA signature (ES384)
     *
     * Simulator:
     *  - public key PEM dari config(\'tenrusl.amzn_bwp_public_key\')
     *  - verify ECDSA SHA-384 terhadap rawBody
     */',
             'namespace' => 'App\\Services\\Signatures',
             'uses' => 
            array (
              'request' => 'Illuminate\\Http\\Request',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'rawBody',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'request',
               'type' => 'Illuminate\\Http\\Request',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Signatures\\DanaSignature.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Services\\Signatures\\DanaSignature',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'verify',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Verify DANA signature (SIMULATOR).
     *
     * Simulator:
     * - Validasi X-SIGNATURE sebagai base64(RSA-SHA256(rawBody)).
     * - Real-world DANA biasanya pakai canonical string (header + body). Bisa kamu ubah di $message.
     */',
             'namespace' => 'App\\Services\\Signatures',
             'uses' => 
            array (
              'request' => 'Illuminate\\Http\\Request',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'rawBody',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'request',
               'type' => 'Illuminate\\Http\\Request',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Signatures\\DokuSignature.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Services\\Signatures\\DokuSignature',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'verify',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Verify DOKU HTTP Notification Signature.
     *
     * Komponen (per baris) mengikuti pola dokumentasi DOKU:
     *  Client-Id:{clientId}
     *  Request-Id:{requestId}
     *  Request-Timestamp:{timestamp}
     *  Request-Target:{requestTarget}
     *  [opsional] Digest:{base64(sha256(body))}
     *
     * Signature:
     *  "HMACSHA256=" + base64( HMAC_SHA256(secretKey, components) )
     */',
             'namespace' => 'App\\Services\\Signatures',
             'uses' => 
            array (
              'request' => 'Illuminate\\Http\\Request',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'rawBody',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'request',
               'type' => 'Illuminate\\Http\\Request',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Signatures\\LemonSqueezySignature.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Services\\Signatures\\LemonSqueezySignature',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'verify',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Verifies Lemon Squeezy webhook signature.
     *
     * Lemon Squeezy mengirim hash payload di header `X-Signature`.
     * Umumnya: HMAC SHA-256 atas raw body menggunakan signing secret.
     */',
             'namespace' => 'App\\Services\\Signatures',
             'uses' => 
            array (
              'request' => 'Illuminate\\Http\\Request',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'rawBody',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'request',
               'type' => 'Illuminate\\Http\\Request',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Signatures\\MidtransSignature.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Services\\Signatures\\MidtransSignature',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'verify',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Verifikasi signature_key Midtrans.
     *
     * Rumus:
     *   signature_key = SHA512(order_id + status_code + gross_amount + serverKey)
     */',
             'namespace' => 'App\\Services\\Signatures',
             'uses' => 
            array (
              'request' => 'Illuminate\\Http\\Request',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'rawBody',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'request',
               'type' => 'Illuminate\\Http\\Request',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Signatures\\MockSignature.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Services\\Signatures\\MockSignature',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'verify',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Verifikasi "mock" untuk keperluan demo.
     *
     * Skema yang diterima:
     *  A) Authorization: Bearer {MOCK_SECRET}
     *  B) X-Mock-Signature: hex(HMAC-SHA256(rawBody, MOCK_SECRET))
     */',
             'namespace' => 'App\\Services\\Signatures',
             'uses' => 
            array (
              'request' => 'Illuminate\\Http\\Request',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'rawBody',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'request',
               'type' => 'Illuminate\\Http\\Request',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Signatures\\OySignature.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Services\\Signatures\\OySignature',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'verify',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * OY! Authorization Callback (simulator-friendly).
     *
     * Skema verifikasi:
     *  - Bearer token: Authorization: "Bearer {OY_CALLBACK_SECRET}"
     *  - Static token: "X-Callback-Auth" atau "X-OY-Callback-Auth"
     *  - HMAC hex: "X-OY-Signature" = HMAC-SHA256(rawBody, OY_CALLBACK_SECRET)
     *
     * Opsional: whitelist IP via env OY_IP_WHITELIST="1.2.3.4,5.6.7.8".
     */',
             'namespace' => 'App\\Services\\Signatures',
             'uses' => 
            array (
              'request' => 'Illuminate\\Http\\Request',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'rawBody',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'request',
               'type' => 'Illuminate\\Http\\Request',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Signatures\\PaddleSignature.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Services\\Signatures\\PaddleSignature',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'verify',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Verifies Paddle webhook signature.
     *
     * Supports:
     * - Paddle Billing (modern): "Paddle-Signature" header with "ts=...,h1=..."
     *   signature = HMAC-SHA256(secret, "{$ts}:{$rawBody}")
     * - (Optional) Paddle Classic: form-POST with "p_signature" (RSA, public key)
     */',
             'namespace' => 'App\\Services\\Signatures',
             'uses' => 
            array (
              'request' => 'Illuminate\\Http\\Request',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'rawBody',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'request',
               'type' => 'Illuminate\\Http\\Request',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Signatures\\PayoneerSignature.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Services\\Signatures\\PayoneerSignature',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'verify',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Payoneer Checkout Notifications (simulator-friendly).
     *
     * Pola verifikasi yang didukung:
     *  - Authorization: Bearer {PAYONEER_SHARED_SECRET}
     *  - X-Payoneer-Signature: hex(HMAC-SHA256(rawBody, PAYONEER_SHARED_SECRET))
     *
     * Opsional: validasi merchant id jika header "X-Payoneer-Merchant-Id" dikirim.
     */',
             'namespace' => 'App\\Services\\Signatures',
             'uses' => 
            array (
              'request' => 'Illuminate\\Http\\Request',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'rawBody',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'request',
               'type' => 'Illuminate\\Http\\Request',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Signatures\\PaypalSignature.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Services\\Signatures\\PaypalSignature',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'verify',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Verifies PayPal webhook via the official Verify Webhook Signature API.
     *
     * Requires headers:
     *  - PAYPAL-TRANSMISSION-ID
     *  - PAYPAL-TRANSMISSION-TIME
     *  - PAYPAL-TRANSMISSION-SIG
     *  - PAYPAL-CERT-URL
     *  - PAYPAL-AUTH-ALGO
     * And config(\'tenrusl.paypal_webhook_id\'), client id/secret.
     */',
             'namespace' => 'App\\Services\\Signatures',
             'uses' => 
            array (
              'request' => 'Illuminate\\Http\\Request',
              'http' => 'Illuminate\\Support\\Facades\\Http',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'rawBody',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'request',
               'type' => 'Illuminate\\Http\\Request',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Signatures\\SignatureVerifier.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Services\\Signatures\\SignatureVerifier',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * SignatureVerifier
 * -----------------
 * Source-of-truth untuk routing verifikasi signature berbasis provider.
 *
 * Kontrak utama:
 * - Dipanggil oleh middleware VerifyWebhookSignature sebelum masuk controller/domain.
 * - Provider harus ada di allowlist config(\'tenrusl.providers_allowlist\').
 * - Tiap verifier provider WAJIB punya method:
 *
 *     public static function verify(string $rawBody, Request $request): bool
 *
 * Catatan penting:
 * - Raw body HARUS yang asli dari Request::getContent() (bukan json_encode hasil decode).
 * - Timestamp leeway (jika provider pakai timestamp) diterapkan di kelas verifier provider
 *   memakai config(\'tenrusl.signature.timestamp_leeway_seconds\', 300).
 * - Bandingkan signature pakai constant-time compare (hash_equals) di verifier provider.
 */',
         'namespace' => 'App\\Services\\Signatures',
         'uses' => 
        array (
          'request' => 'Illuminate\\Http\\Request',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'verify',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Verifikasi signature untuk provider tertentu.
     *
     * @param  string  $provider  Nama provider (mock, xendit, midtrans, dst)
     * @param  string  $rawBody  Raw HTTP body dari Request::getContent()
     * @param  Request  $request  Request Laravel (untuk akses header, query, ip, dsb.)
     */',
             'namespace' => 'App\\Services\\Signatures',
             'uses' => 
            array (
              'request' => 'Illuminate\\Http\\Request',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'provider',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'rawBody',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'request',
               'type' => 'Illuminate\\Http\\Request',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'supported',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Daftar provider yang *disupport oleh verifier layer ini*.
     * Jika allowlist diset, hasilnya adalah interseksi MAP vs allowlist.
     *
     * @return string[]
     */',
             'namespace' => 'App\\Services\\Signatures',
             'uses' => 
            array (
              'request' => 'Illuminate\\Http\\Request',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Signatures\\SkrillSignature.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Services\\Signatures\\SkrillSignature',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'verify',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Verifikasi IPN/Status URL Skrill.
     *
     * md5sig = UPPERCASE(
     *   MD5( merchant_id . transaction_id . UPPERCASE(MD5(secret_word)) . mb_amount . mb_currency . status )
     * )
     *
     * sha2sig (opsional) dibentuk sama, namun menggunakan SHA-256 (over string yang sama).
     *
     * Catatan keamanan:
     * - Minimal salah satu signature (md5sig/sha2sig) harus ada.
     */',
             'namespace' => 'App\\Services\\Signatures',
             'uses' => 
            array (
              'request' => 'Illuminate\\Http\\Request',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'rawBody',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'request',
               'type' => 'Illuminate\\Http\\Request',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Signatures\\StripeSignature.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Services\\Signatures\\StripeSignature',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'verify',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Verifies Stripe webhook signature.
     *
     * Stripe signs the raw payload with:
     *   expected = HMAC-SHA256(secret, "{$t}.{$rawBody}")
     *
     * Header "Stripe-Signature" format:
     *   t=TIMESTAMP,v1=HEX[,v1=HEX...]
     */',
             'namespace' => 'App\\Services\\Signatures',
             'uses' => 
            array (
              'request' => 'Illuminate\\Http\\Request',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'rawBody',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'request',
               'type' => 'Illuminate\\Http\\Request',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Signatures\\TripaySignature.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Services\\Signatures\\TripaySignature',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'verify',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Verify TriPay callback signature.
     *
     * Header `X-Callback-Signature` berisi:
     *   hex(HMAC-SHA256(rawBody, private_key))
     */',
             'namespace' => 'App\\Services\\Signatures',
             'uses' => 
            array (
              'request' => 'Illuminate\\Http\\Request',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'rawBody',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'request',
               'type' => 'Illuminate\\Http\\Request',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Signatures\\XenditSignature.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Services\\Signatures\\XenditSignature',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'verify',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Xendit webhook verification:
     * - Token ada di header `x-callback-token` (atau variasi kapitalisasi)
     * - Cocokkan dengan token di config/env kamu.
     *
     * Catatan: $rawBody tidak dipakai untuk token-based verification,
     * tapi dipertahankan agar signature interface konsisten.
     */',
             'namespace' => 'App\\Services\\Signatures',
             'uses' => 
            array (
              'request' => 'Illuminate\\Http\\Request',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'rawBody',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'request',
               'type' => 'Illuminate\\Http\\Request',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Webhooks\\RetryBackoff.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Services\\Webhooks\\RetryBackoff',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Kalkulasi exponential backoff + jitter.
 *
 * Mode:
 *  - \'full\'          : FULL JITTER   -> random(0, exp)
 *  - \'equal\'         : EQUAL JITTER  -> exp/2 + random(0, exp/2)
 *  - \'decorrelated\'  : DECORRELATED  -> min(cap, random(base, prev*3))
 *
 * Nilai balik dalam milidetik (ms).
 *
 * Referensi konsep jitter ini umum dipakai di sistem retry agar:
 * - menghindari thundering herd (semua retry di waktu yg sama),
 * - lebih stabil saat provider webhook burst / timeout.
 */',
         'namespace' => 'App\\Services\\Webhooks',
         'uses' => 
        array (
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'normalizeMode',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Normalisasi input mode agar tolerant terhadap input user/env.
     */',
             'namespace' => 'App\\Services\\Webhooks',
             'uses' => 
            array (
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'string',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'mode',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'compute',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Hitung delay (ms) untuk sebuah attempt.
     *
     * Konvensi yang dipakai:
     * - attempt dimulai dari 1
     * - exp tanpa jitter: base * 2^(attempt-1) lalu di-cap ke capMs
     *
     * @param  int  $attempt  attempt ke- (mulai 1)
     * @param  int  $baseMs  base delay dalam ms
     * @param  int  $capMs  batas maksimal delay
     * @param  string  $mode  full|equal|decorrelated
     * @param  int|null  $maxAttempts  bila diisi, clamp attempt ke maxAttempts
     * @param  int|null  $prevMs  (opsional) delay sebelumnya utk decorrelated
     */',
             'namespace' => 'App\\Services\\Webhooks',
             'uses' => 
            array (
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'int',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'attempt',
               'type' => 'int',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'baseMs',
               'type' => 'int',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'capMs',
               'type' => 'int',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
            )),
            3 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'mode',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
            )),
            4 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'maxAttempts',
               'type' => '?int',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
            )),
            5 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'prevMs',
               'type' => '?int',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'schedule',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Buat daftar jadwal retry (ms) untuk N attempt.
     * Berguna untuk debugging/README: lihat pattern delay.
     *
     * @return array<int,int> key=attempt (mulai 1) => delay(ms)
     */',
             'namespace' => 'App\\Services\\Webhooks',
             'uses' => 
            array (
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'maxAttempts',
               'type' => 'int',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'baseMs',
               'type' => 'int',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'capMs',
               'type' => 'int',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
            )),
            3 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'mode',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Services\\Webhooks\\WebhookProcessor.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Services\\Webhooks\\WebhookProcessor',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__construct',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'events',
               'type' => 'App\\Repositories\\WebhookEventRepository',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'payments',
               'type' => 'App\\Repositories\\PaymentRepository',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'process',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return array{
     *   duplicate:bool,
     *   persisted:bool,
     *   status:string,
     *   payment_provider_ref:string|null,
     *   next_retry_ms:int|null
     * }
     */',
             'namespace' => 'App\\Services\\Webhooks',
             'uses' => 
            array (
              'paymentrepository' => 'App\\Repositories\\PaymentRepository',
              'webhookeventrepository' => 'App\\Repositories\\WebhookEventRepository',
              'paymentstatus' => 'App\\ValueObjects\\PaymentStatus',
              'backedenum' => 'BackedEnum',
              'carbonimmutable' => 'Carbon\\CarbonImmutable',
              'carboninterface' => 'Carbon\\CarbonInterface',
              'arr' => 'Illuminate\\Support\\Arr',
              'db' => 'Illuminate\\Support\\Facades\\DB',
              'log' => 'Illuminate\\Support\\Facades\\Log',
              'throwable' => 'Throwable',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'provider',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'eventId',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'type',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            3 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'rawBody',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            4 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'payload',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Support\\Clock.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Support\\Clock',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Abstraksi waktu sederhana agar mudah di-test.
 * Default memakai CarbonImmutable::now(); bisa di-freeze / travel via setTestNow().
 */',
         'namespace' => 'App\\Support',
         'uses' => 
        array (
          'carbonimmutable' => 'Carbon\\CarbonImmutable',
          'datetimeinterface' => 'DateTimeInterface',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'now',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Waktu saat ini (ikut testNow jika di-set).
     */',
             'namespace' => 'App\\Support',
             'uses' => 
            array (
              'carbonimmutable' => 'Carbon\\CarbonImmutable',
              'datetimeinterface' => 'DateTimeInterface',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'Carbon\\CarbonImmutable',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'tz',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'utc',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Waktu sekarang dalam UTC.
     */',
             'namespace' => 'App\\Support',
             'uses' => 
            array (
              'carbonimmutable' => 'Carbon\\CarbonImmutable',
              'datetimeinterface' => 'DateTimeInterface',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'Carbon\\CarbonImmutable',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'setTestNow',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Set testNow (membekukan waktu) atau kosongkan untuk kembali realtime.
     */',
             'namespace' => 'App\\Support',
             'uses' => 
            array (
              'carbonimmutable' => 'Carbon\\CarbonImmutable',
              'datetimeinterface' => 'DateTimeInterface',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'now',
               'type' => '?Carbon\\CarbonImmutable',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'freeze',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Bekukan waktu ke saat ini.
     */',
             'namespace' => 'App\\Support',
             'uses' => 
            array (
              'carbonimmutable' => 'Carbon\\CarbonImmutable',
              'datetimeinterface' => 'DateTimeInterface',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'void',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'travel',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Geser waktu uji (positif = ke depan; negatif = ke belakang).
     */',
             'namespace' => 'App\\Support',
             'uses' => 
            array (
              'carbonimmutable' => 'Carbon\\CarbonImmutable',
              'datetimeinterface' => 'DateTimeInterface',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'seconds',
               'type' => 'int',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        5 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'clear',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Hapus testNow.
     */',
             'namespace' => 'App\\Support',
             'uses' => 
            array (
              'carbonimmutable' => 'Carbon\\CarbonImmutable',
              'datetimeinterface' => 'DateTimeInterface',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'void',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        6 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'parse',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Parse string/DateTime menjadi CarbonImmutable.
     */',
             'namespace' => 'App\\Support',
             'uses' => 
            array (
              'carbonimmutable' => 'Carbon\\CarbonImmutable',
              'datetimeinterface' => 'DateTimeInterface',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'Carbon\\CarbonImmutable',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'time',
               'type' => 'string|DateTimeInterface',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'tz',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Support\\Json.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Support\\Json',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Helper JSON aman:
 * - encode(): aktifkan JSON_THROW_ON_ERROR + unescaped unicode/slashes
 * - decode(): gunakan JSON_THROW_ON_ERROR
 * - tryDecode(), isJson(): utilitas non-throw
 */',
         'namespace' => 'App\\Support',
         'uses' => 
        array (
          'jsonexception' => 'JsonException',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'encode',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @throws JsonException
     */',
             'namespace' => 'App\\Support',
             'uses' => 
            array (
              'jsonexception' => 'JsonException',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'string',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'value',
               'type' => 'mixed',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'flags',
               'type' => 'int',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'depth',
               'type' => 'int',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'decode',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @throws JsonException
     */',
             'namespace' => 'App\\Support',
             'uses' => 
            array (
              'jsonexception' => 'JsonException',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'mixed',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'json',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'assoc',
               'type' => 'bool',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'depth',
               'type' => 'int',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'tryDecode',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return mixed|null null jika gagal decode
     */',
             'namespace' => 'App\\Support',
             'uses' => 
            array (
              'jsonexception' => 'JsonException',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'mixed',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'json',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'assoc',
               'type' => 'bool',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'depth',
               'type' => 'int',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'isJson',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Periksa cepat apakah string berformat JSON valid.
     */',
             'namespace' => 'App\\Support',
             'uses' => 
            array (
              'jsonexception' => 'JsonException',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'value',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\Traits\\HasUlid.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedTraitNode::__set_state(array(
       'name' => 'App\\Traits\\HasUlid',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Trait untuk primary key berbasis ULID pada Eloquent Model.
 *
 * - Menyetel $incrementing=false dan $keyType=\'string\'
 * - Mengisi kolom kunci (getKeyName()) dengan ULID saat creating jika kosong
 */',
         'namespace' => 'App\\Traits',
         'uses' => 
        array (
          'str' => 'Illuminate\\Support\\Str',
        ),
         'constUses' => 
        array (
        ),
      )),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'initializeHasUlid',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Inisialisasi properti model saat trait dipakai.
     */',
             'namespace' => 'App\\Traits',
             'uses' => 
            array (
              'str' => 'Illuminate\\Support\\Str',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'bootHasUlid',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Hook Eloquent: isi kunci ULID saat creating jika belum ada.
     */',
             'namespace' => 'App\\Traits',
             'uses' => 
            array (
              'str' => 'Illuminate\\Support\\Str',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'void',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'getUlid',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Helper untuk memperoleh nilai ULID dari model.
     */',
             'namespace' => 'App\\Traits',
             'uses' => 
            array (
              'str' => 'Illuminate\\Support\\Str',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\app\\ValueObjects\\PaymentStatus.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedEnumNode::__set_state(array(
       'name' => 'App\\ValueObjects\\PaymentStatus',
       'scalarType' => 'string',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Status pembayaran generik untuk simulator.
 *
 * Gunakan PaymentStatus::fromString($anyProviderStatus) untuk memetakan
 * berbagai status provider ke 3 status inti: pending|succeeded|failed.
 */',
         'namespace' => 'App\\ValueObjects',
         'uses' => 
        array (
        ),
         'constUses' => 
        array (
        ),
      )),
       'implements' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedEnumCaseNode::__set_state(array(
           'name' => 'Pending',
           'value' => '\'pending\'',
           'phpDoc' => NULL,
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedEnumCaseNode::__set_state(array(
           'name' => 'Succeeded',
           'value' => '\'succeeded\'',
           'phpDoc' => NULL,
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedEnumCaseNode::__set_state(array(
           'name' => 'Failed',
           'value' => '\'failed\'',
           'phpDoc' => NULL,
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'fromString',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Normalisasi dari status provider apa pun ke 3 status inti.
     */',
             'namespace' => 'App\\ValueObjects',
             'uses' => 
            array (
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'self',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'value',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'isPending',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        5 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'isSucceeded',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        6 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'isFailed',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        7 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'isFinal',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        8 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'toString',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\database\\factories\\PaymentFactory.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'Database\\Factories\\PaymentFactory',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Database\\Eloquent\\Factories\\Factory',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'model',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'definition',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'pending',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'static',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'succeeded',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'static',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'failed',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'static',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\database\\factories\\UserFactory.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'Database\\Factories\\UserFactory',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * @extends \\Illuminate\\Database\\Eloquent\\Factories\\Factory<\\App\\Models\\User>
 */',
         'namespace' => 'Database\\Factories',
         'uses' => 
        array (
          'factory' => 'Illuminate\\Database\\Eloquent\\Factories\\Factory',
          'hash' => 'Illuminate\\Support\\Facades\\Hash',
          'str' => 'Illuminate\\Support\\Str',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Database\\Eloquent\\Factories\\Factory',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'password',
          ),
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * The current password being used by the factory.
     */',
             'namespace' => 'Database\\Factories',
             'uses' => 
            array (
              'factory' => 'Illuminate\\Database\\Eloquent\\Factories\\Factory',
              'hash' => 'Illuminate\\Support\\Facades\\Hash',
              'str' => 'Illuminate\\Support\\Str',
            ),
             'constUses' => 
            array (
            ),
          )),
           'type' => '?string',
           'public' => false,
           'private' => false,
           'static' => true,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'definition',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Define the model\'s default state.
     *
     * @return array<string, mixed>
     */',
             'namespace' => 'Database\\Factories',
             'uses' => 
            array (
              'factory' => 'Illuminate\\Database\\Eloquent\\Factories\\Factory',
              'hash' => 'Illuminate\\Support\\Facades\\Hash',
              'str' => 'Illuminate\\Support\\Str',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'unverified',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Indicate that the model\'s email address should be unverified.
     */',
             'namespace' => 'Database\\Factories',
             'uses' => 
            array (
              'factory' => 'Illuminate\\Database\\Eloquent\\Factories\\Factory',
              'hash' => 'Illuminate\\Support\\Facades\\Hash',
              'str' => 'Illuminate\\Support\\Str',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'static',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\database\\factories\\WebhookEventFactory.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'Database\\Factories\\WebhookEventFactory',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Database\\Eloquent\\Factories\\Factory',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'model',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'definition',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'received',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'static',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'processed',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'static',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'failed',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'static',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\database\\seeders\\DatabaseSeeder.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'Database\\Seeders\\DatabaseSeeder',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => 'Illuminate\\Database\\Seeder',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'run',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\CreatesApplication.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedTraitNode::__set_state(array(
       'name' => 'Tests\\CreatesApplication',
       'phpDoc' => NULL,
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'createApplication',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Bootstrap aplikasi untuk testing.
     *
     * @return \\Illuminate\\Foundation\\Application
     */',
             'namespace' => 'Tests',
             'uses' => 
            array (
              'kernel' => 'Illuminate\\Contracts\\Console\\Kernel',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\Feature\\WebhookReceiverTest.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedFunctionNode::__set_state(array(
       'name' => 'mockSignature',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Helper: bikin signature mock yang “paling umum” dipakai: HMAC-SHA256(raw body, secret).
 */',
         'namespace' => NULL,
         'uses' => 
        array (
          'processwebhookevent' => 'App\\Jobs\\ProcessWebhookEvent',
          'webhookevent' => 'App\\Models\\WebhookEvent',
          'refreshdatabase' => 'Illuminate\\Foundation\\Testing\\RefreshDatabase',
          'carbon' => 'Illuminate\\Support\\Carbon',
          'artisan' => 'Illuminate\\Support\\Facades\\Artisan',
          'bus' => 'Illuminate\\Support\\Facades\\Bus',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'str' => 'Illuminate\\Support\\Str',
        ),
         'constUses' => 
        array (
        ),
      )),
       'byRef' => false,
       'returnType' => 'string',
       'parameters' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
           'name' => 'rawBody',
           'type' => 'string',
           'byRef' => false,
           'variadic' => false,
           'hasDefault' => false,
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\TenRusl-Payment-Webhook-sim\\tests\\TestCase.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'Tests\\TestCase',
       'phpDoc' => NULL,
       'abstract' => true,
       'final' => false,
       'extends' => 'Illuminate\\Foundation\\Testing\\TestCase',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'Tests\\CreatesApplication',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
); },
];
