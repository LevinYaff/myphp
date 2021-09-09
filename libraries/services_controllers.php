<?php

declare(strict_types=1);

return [
    'services' => [
        PhpMyAdmin\Controllers\BrowseForeignersController::class => [
            'class' => PhpMyAdmin\Controllers\BrowseForeignersController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$browseForeigners' => '@browse_foreigners',
                '$relation' => '@relation',
            ],
        ],
        PhpMyAdmin\Controllers\ChangeLogController::class => [
            'class' => PhpMyAdmin\Controllers\ChangeLogController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
            ],
        ],
        PhpMyAdmin\Controllers\CheckRelationsController::class => [
            'class' => PhpMyAdmin\Controllers\CheckRelationsController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$relation' => '@relation',
            ],
        ],
        PhpMyAdmin\Controllers\CollationConnectionController::class => [
            'class' => PhpMyAdmin\Controllers\CollationConnectionController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$config' => '@config',
            ],
        ],
        PhpMyAdmin\Controllers\ColumnController::class => [
            'class' => PhpMyAdmin\Controllers\ColumnController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Config\GetConfigController::class => [
            'class' => PhpMyAdmin\Controllers\Config\GetConfigController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$config' => '@config',
            ],
        ],
        PhpMyAdmin\Controllers\Config\SetConfigController::class => [
            'class' => PhpMyAdmin\Controllers\Config\SetConfigController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$config' => '@config',
            ],
        ],
        PhpMyAdmin\Controllers\Database\CentralColumns\PopulateColumnsController::class => [
            'class' => PhpMyAdmin\Controllers\Database\CentralColumns\PopulateColumnsController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$centralColumns' => '@central_columns',
            ],
        ],
        PhpMyAdmin\Controllers\Database\CentralColumnsController::class => [
            'class' => PhpMyAdmin\Controllers\Database\CentralColumnsController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$centralColumns' => '@central_columns',
            ],
        ],
        PhpMyAdmin\Controllers\Database\DataDictionaryController::class => [
            'class' => PhpMyAdmin\Controllers\Database\DataDictionaryController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$relation' => '@relation',
                '$transformations' => '@transformations',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Database\DesignerController::class => [
            'class' => PhpMyAdmin\Controllers\Database\DesignerController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$databaseDesigner' => '@designer',
                '$designerCommon' => '@designer_common',
            ],
        ],
        PhpMyAdmin\Controllers\Database\EventsController::class => [
            'class' => PhpMyAdmin\Controllers\Database\EventsController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$events' => '@events',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Database\ExportController::class => [
            'class' => PhpMyAdmin\Controllers\Database\ExportController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$export' => '@export',
                '$exportOptions' => '@export_options',
            ],
        ],
        PhpMyAdmin\Controllers\Database\ImportController::class => [
            'class' => PhpMyAdmin\Controllers\Database\ImportController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Database\MultiTableQuery\QueryController::class => [
            'class' => PhpMyAdmin\Controllers\Database\MultiTableQuery\QueryController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
            ],
        ],
        PhpMyAdmin\Controllers\Database\MultiTableQuery\TablesController::class => [
            'class' => PhpMyAdmin\Controllers\Database\MultiTableQuery\TablesController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Database\MultiTableQueryController::class => [
            'class' => PhpMyAdmin\Controllers\Database\MultiTableQueryController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Database\Operations\CollationController::class => [
            'class' => PhpMyAdmin\Controllers\Database\Operations\CollationController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$operations' => '@operations',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Database\OperationsController::class => [
            'class' => PhpMyAdmin\Controllers\Database\OperationsController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$operations' => '@operations',
                '$checkUserPrivileges' => '@check_user_privileges',
                '$relation' => '@relation',
                '$relationCleanup' => '@relation_cleanup',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Database\PrivilegesController::class => [
            'class' => PhpMyAdmin\Controllers\Database\PrivilegesController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$privileges' => '@server_privileges',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Database\QueryByExampleController::class => [
            'class' => PhpMyAdmin\Controllers\Database\QueryByExampleController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$relation' => '@relation',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Database\RoutinesController::class => [
            'class' => PhpMyAdmin\Controllers\Database\RoutinesController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$checkUserPrivileges' => '@check_user_privileges',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Database\SearchController::class => [
            'class' => PhpMyAdmin\Controllers\Database\SearchController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Database\SqlAutoCompleteController::class => [
            'class' => PhpMyAdmin\Controllers\Database\SqlAutoCompleteController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Database\SqlController::class => [
            'class' => PhpMyAdmin\Controllers\Database\SqlController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$sqlQueryForm' => '@sql_query_form',
            ],
        ],
        PhpMyAdmin\Controllers\Database\SqlFormatController::class => [
            'class' => PhpMyAdmin\Controllers\Database\SqlFormatController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
            ],
        ],
        PhpMyAdmin\Controllers\Database\Structure\AddPrefixController::class => [
            'class' => PhpMyAdmin\Controllers\Database\Structure\AddPrefixController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
            ],
        ],
        PhpMyAdmin\Controllers\Database\Structure\AddPrefixTableController::class => [
            'class' => PhpMyAdmin\Controllers\Database\Structure\AddPrefixTableController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$dbi' => '@dbi',
                '$structureController' => '@' . PhpMyAdmin\Controllers\Database\StructureController::class,
            ],
        ],
        PhpMyAdmin\Controllers\Database\Structure\CentralColumns\AddController::class => [
            'class' => PhpMyAdmin\Controllers\Database\Structure\CentralColumns\AddController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$dbi' => '@dbi',
                '$structureController' => '@' . PhpMyAdmin\Controllers\Database\StructureController::class,
            ],
        ],
        PhpMyAdmin\Controllers\Database\Structure\CentralColumns\MakeConsistentController::class => [
            'class' => PhpMyAdmin\Controllers\Database\Structure\CentralColumns\MakeConsistentController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$dbi' => '@dbi',
                '$structureController' => '@' . PhpMyAdmin\Controllers\Database\StructureController::class,
            ],
        ],
        PhpMyAdmin\Controllers\Database\Structure\CentralColumns\RemoveController::class => [
            'class' => PhpMyAdmin\Controllers\Database\Structure\CentralColumns\RemoveController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$dbi' => '@dbi',
                '$structureController' => '@' . PhpMyAdmin\Controllers\Database\StructureController::class,
            ],
        ],
        PhpMyAdmin\Controllers\Database\Structure\ChangePrefixFormController::class => [
            'class' => PhpMyAdmin\Controllers\Database\Structure\ChangePrefixFormController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
            ],
        ],
        PhpMyAdmin\Controllers\Database\Structure\CopyFormController::class => [
            'class' => PhpMyAdmin\Controllers\Database\Structure\CopyFormController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
            ],
        ],
        PhpMyAdmin\Controllers\Database\Structure\CopyTableController::class => [
            'class' => PhpMyAdmin\Controllers\Database\Structure\CopyTableController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$operations' => '@operations',
                '$structureController' => '@' . PhpMyAdmin\Controllers\Database\StructureController::class,
            ],
        ],
        PhpMyAdmin\Controllers\Database\Structure\CopyTableWithPrefixController::class => [
            'class' => PhpMyAdmin\Controllers\Database\Structure\CopyTableWithPrefixController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$structureController' => '@' . PhpMyAdmin\Controllers\Database\StructureController::class,
            ],
        ],
        PhpMyAdmin\Controllers\Database\Structure\DropFormController::class => [
            'class' => PhpMyAdmin\Controllers\Database\Structure\DropFormController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Database\Structure\DropTableController::class => [
            'class' => PhpMyAdmin\Controllers\Database\Structure\DropTableController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$dbi' => '@dbi',
                '$relationCleanup' => '@relation_cleanup',
                '$structureController' => '@' . PhpMyAdmin\Controllers\Database\StructureController::class,
            ],
        ],
        PhpMyAdmin\Controllers\Database\Structure\EmptyFormController::class => [
            'class' => PhpMyAdmin\Controllers\Database\Structure\EmptyFormController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
            ],
        ],
        PhpMyAdmin\Controllers\Database\Structure\EmptyTableController::class => [
            'class' => PhpMyAdmin\Controllers\Database\Structure\EmptyTableController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$dbi' => '@dbi',
                '$relation' => '@relation',
                '$relationCleanup' => '@relation_cleanup',
                '$operations' => '@operations',
                '$flash' => '@flash',
                '$structureController' => '@' . PhpMyAdmin\Controllers\Database\StructureController::class,
            ],
        ],
        PhpMyAdmin\Controllers\Database\Structure\FavoriteTableController::class => [
            'class' => PhpMyAdmin\Controllers\Database\Structure\FavoriteTableController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$relation' => '@relation',
            ],
        ],
        PhpMyAdmin\Controllers\Database\Structure\RealRowCountController::class => [
            'class' => PhpMyAdmin\Controllers\Database\Structure\RealRowCountController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Database\Structure\ReplacePrefixController::class => [
            'class' => PhpMyAdmin\Controllers\Database\Structure\ReplacePrefixController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$dbi' => '@dbi',
                '$structureController' => '@' . PhpMyAdmin\Controllers\Database\StructureController::class,
            ],
        ],
        PhpMyAdmin\Controllers\Database\Structure\ShowCreateController::class => [
            'class' => PhpMyAdmin\Controllers\Database\Structure\ShowCreateController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Database\StructureController::class => [
            'class' => PhpMyAdmin\Controllers\Database\StructureController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$relation' => '@relation',
                '$replication' => '@replication',
                '$relationCleanup' => '@relation_cleanup',
                '$operations' => '@operations',
                '$dbi' => '@dbi',
                '$flash' => '@flash',
            ],
        ],
        PhpMyAdmin\Controllers\Database\TrackingController::class => [
            'class' => PhpMyAdmin\Controllers\Database\TrackingController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$tracking' => '@tracking',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Database\TriggersController::class => [
            'class' => PhpMyAdmin\Controllers\Database\TriggersController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\DatabaseController::class => [
            'class' => PhpMyAdmin\Controllers\DatabaseController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
            ],
        ],
        PhpMyAdmin\Controllers\ErrorReportController::class => [
            'class' => PhpMyAdmin\Controllers\ErrorReportController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$errorReport' => '@error_report',
                '$errorHandler' => '@error_handler',
            ],
        ],
        PhpMyAdmin\Controllers\Export\CheckTimeOutController::class => [
            'class' => PhpMyAdmin\Controllers\Export\CheckTimeOutController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
            ],
        ],
        PhpMyAdmin\Controllers\Export\ExportController::class => [
            'class' => PhpMyAdmin\Controllers\Export\ExportController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$export' => '@export',
                '$relation' => '@relation',
            ],
        ],
        PhpMyAdmin\Controllers\Export\TablesController::class => [
            'class' => PhpMyAdmin\Controllers\Export\TablesController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$exportController' => '@' . PhpMyAdmin\Controllers\Database\ExportController::class,
            ],
        ],
        PhpMyAdmin\Controllers\Export\Template\CreateController::class => [
            'class' => PhpMyAdmin\Controllers\Export\Template\CreateController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$model' => '@export_template_model',
                '$relation' => '@relation',
            ],
        ],
        PhpMyAdmin\Controllers\Export\Template\DeleteController::class => [
            'class' => PhpMyAdmin\Controllers\Export\Template\DeleteController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$model' => '@export_template_model',
                '$relation' => '@relation',
            ],
        ],
        PhpMyAdmin\Controllers\Export\Template\LoadController::class => [
            'class' => PhpMyAdmin\Controllers\Export\Template\LoadController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$model' => '@export_template_model',
                '$relation' => '@relation',
            ],
        ],
        PhpMyAdmin\Controllers\Export\Template\UpdateController::class => [
            'class' => PhpMyAdmin\Controllers\Export\Template\UpdateController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$model' => '@export_template_model',
                '$relation' => '@relation',
            ],
        ],
        PhpMyAdmin\Controllers\GisDataEditorController::class => [
            'class' => PhpMyAdmin\Controllers\GisDataEditorController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
            ],
        ],
        PhpMyAdmin\Controllers\GitInfoController::class => [
            'class' => PhpMyAdmin\Controllers\GitInfoController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$config' => '@config',
            ],
        ],
        PhpMyAdmin\Controllers\HomeController::class => [
            'class' => PhpMyAdmin\Controllers\HomeController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$config' => '@config',
                '$themeManager' => '@theme_manager',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\ImportController::class => [
            'class' => PhpMyAdmin\Controllers\ImportController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$import' => '@import',
                '$sql' => '@sql',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\ImportStatusController::class => [
            'class' => PhpMyAdmin\Controllers\ImportStatusController::class,
            'arguments' => ['$template' => '@template'],
        ],
        PhpMyAdmin\Controllers\JavaScriptMessagesController::class => [
            'class' => PhpMyAdmin\Controllers\JavaScriptMessagesController::class,
        ],
        PhpMyAdmin\Controllers\LicenseController::class => [
            'class' => PhpMyAdmin\Controllers\LicenseController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
            ],
        ],
        PhpMyAdmin\Controllers\LintController::class => [
            'class' => PhpMyAdmin\Controllers\LintController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
            ],
        ],
        PhpMyAdmin\Controllers\LogoutController::class => [
            'class' => PhpMyAdmin\Controllers\LogoutController::class,
        ],
        PhpMyAdmin\Controllers\NavigationController::class => [
            'class' => PhpMyAdmin\Controllers\NavigationController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$navigation' => '@navigation',
                '$relation' => '@relation',
            ],
        ],
        PhpMyAdmin\Controllers\NormalizationController::class => [
            'class' => PhpMyAdmin\Controllers\NormalizationController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$normalization' => '@normalization',
            ],
        ],
        PhpMyAdmin\Controllers\PhpInfoController::class => [
            'class' => PhpMyAdmin\Controllers\PhpInfoController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
            ],
        ],
        PhpMyAdmin\Controllers\RecentTablesListController::class => [
            'class' => PhpMyAdmin\Controllers\RecentTablesListController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
            ],
        ],
        PhpMyAdmin\Controllers\Preferences\ExportController::class => [
            'class' => PhpMyAdmin\Controllers\Preferences\ExportController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$userPreferences' => '@user_preferences',
                '$relation' => '@relation',
                '$config' => '@config',
            ],
        ],
        PhpMyAdmin\Controllers\Preferences\FeaturesController::class => [
            'class' => PhpMyAdmin\Controllers\Preferences\FeaturesController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$userPreferences' => '@user_preferences',
                '$relation' => '@relation',
                '$config' => '@config',
            ],
        ],
        PhpMyAdmin\Controllers\Preferences\ImportController::class => [
            'class' => PhpMyAdmin\Controllers\Preferences\ImportController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$userPreferences' => '@user_preferences',
                '$relation' => '@relation',
                '$config' => '@config',
            ],
        ],
        PhpMyAdmin\Controllers\Preferences\MainPanelController::class => [
            'class' => PhpMyAdmin\Controllers\Preferences\MainPanelController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$userPreferences' => '@user_preferences',
                '$relation' => '@relation',
                '$config' => '@config',
            ],
        ],
        PhpMyAdmin\Controllers\Preferences\ManageController::class => [
            'class' => PhpMyAdmin\Controllers\Preferences\ManageController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$userPreferences' => '@user_preferences',
                '$relation' => '@relation',
                '$config' => '@config',
            ],
        ],
        PhpMyAdmin\Controllers\Preferences\NavigationController::class => [
            'class' => PhpMyAdmin\Controllers\Preferences\NavigationController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$userPreferences' => '@user_preferences',
                '$relation' => '@relation',
                '$config' => '@config',
            ],
        ],
        PhpMyAdmin\Controllers\Preferences\SqlController::class => [
            'class' => PhpMyAdmin\Controllers\Preferences\SqlController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$userPreferences' => '@user_preferences',
                '$relation' => '@relation',
                '$config' => '@config',
            ],
        ],
        PhpMyAdmin\Controllers\Preferences\TwoFactorController::class => [
            'class' => PhpMyAdmin\Controllers\Preferences\TwoFactorController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$relation' => '@relation',
            ],
        ],
        PhpMyAdmin\Controllers\SchemaExportController::class => [
            'class' => PhpMyAdmin\Controllers\SchemaExportController::class,
            'arguments' => [
                '$export' => '@export',
                '$relation' => '@relation',
            ],
        ],
        PhpMyAdmin\Controllers\Server\BinlogController::class => [
            'class' => PhpMyAdmin\Controllers\Server\BinlogController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Server\CollationsController::class => [
            'class' => PhpMyAdmin\Controllers\Server\CollationsController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Server\Databases\CreateController::class => [
            'class' => PhpMyAdmin\Controllers\Server\Databases\CreateController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Server\Databases\DestroyController::class => [
            'class' => PhpMyAdmin\Controllers\Server\Databases\DestroyController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$dbi' => '@dbi',
                '$transformations' => '@transformations',
                '$relationCleanup' => '@relation_cleanup',
            ],
        ],
        PhpMyAdmin\Controllers\Server\DatabasesController::class => [
            'class' => PhpMyAdmin\Controllers\Server\DatabasesController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$transformations' => '@transformations',
                '$relationCleanup' => '@relation_cleanup',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Server\EnginesController::class => [
            'class' => PhpMyAdmin\Controllers\Server\EnginesController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Server\ExportController::class => [
            'class' => PhpMyAdmin\Controllers\Server\ExportController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$export' => '@export_options',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Server\ImportController::class => [
            'class' => PhpMyAdmin\Controllers\Server\ImportController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Server\PluginsController::class => [
            'class' => PhpMyAdmin\Controllers\Server\PluginsController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$plugins' => '@server_plugins',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Server\Privileges\AccountLockController::class => [
            'class' => PhpMyAdmin\Controllers\Server\Privileges\AccountLockController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$accountLocking' => '@server_privileges_account_locking',
            ],
        ],
        PhpMyAdmin\Controllers\Server\Privileges\AccountUnlockController::class => [
            'class' => PhpMyAdmin\Controllers\Server\Privileges\AccountUnlockController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$accountLocking' => '@server_privileges_account_locking',
            ],
        ],
        PhpMyAdmin\Controllers\Server\PrivilegesController::class => [
            'class' => PhpMyAdmin\Controllers\Server\PrivilegesController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$relation' => '@relation',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Server\ReplicationController::class => [
            'class' => PhpMyAdmin\Controllers\Server\ReplicationController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$replicationGui' => '@replication_gui',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Server\SqlController::class => [
            'class' => PhpMyAdmin\Controllers\Server\SqlController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$sqlQueryForm' => '@sql_query_form',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Server\UserGroupsController::class => [
            'class' => PhpMyAdmin\Controllers\Server\UserGroupsController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$relation' => '@relation',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Server\Status\AdvisorController::class => [
            'class' => PhpMyAdmin\Controllers\Server\Status\AdvisorController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$data' => '@status_data',
                '$advisor' => '@advisor',
            ],
        ],
        PhpMyAdmin\Controllers\Server\Status\MonitorController::class => [
            'class' => PhpMyAdmin\Controllers\Server\Status\MonitorController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$data' => '@status_data',
                '$monitor' => '@status_monitor',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Server\Status\ProcessesController::class => [
            'class' => PhpMyAdmin\Controllers\Server\Status\ProcessesController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$data' => '@status_data',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Server\Status\QueriesController::class => [
            'class' => PhpMyAdmin\Controllers\Server\Status\QueriesController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$data' => '@status_data',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Server\Status\StatusController::class => [
            'class' => PhpMyAdmin\Controllers\Server\Status\StatusController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$data' => '@status_data',
                '$replicationGui' => '@replication_gui',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Server\Status\VariablesController::class => [
            'class' => PhpMyAdmin\Controllers\Server\Status\VariablesController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$data' => '@status_data',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Server\VariablesController::class => [
            'class' => PhpMyAdmin\Controllers\Server\VariablesController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\SqlController::class => [
            'class' => PhpMyAdmin\Controllers\SqlController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$sql' => '@sql',
                '$checkUserPrivileges' => '@check_user_privileges',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Table\AddFieldController::class => [
            'class' => PhpMyAdmin\Controllers\Table\AddFieldController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$table' => '%table%',
                '$transformations' => '@transformations',
                '$config' => '@config',
                '$relation' => '@relation',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Table\ChangeController::class => [
            'class' => PhpMyAdmin\Controllers\Table\ChangeController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$table' => '%table%',
                '$insertEdit' => '@insert_edit',
                '$relation' => '@relation',
            ],
        ],
        PhpMyAdmin\Controllers\Table\ChartController::class => [
            'class' => PhpMyAdmin\Controllers\Table\ChartController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$table' => '%table%',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Table\CreateController::class => [
            'class' => PhpMyAdmin\Controllers\Table\CreateController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$table' => '%table%',
                '$transformations' => '@transformations',
                '$config' => '@config',
                '$relation' => '@relation',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Table\DeleteController::class => [
            'class' => PhpMyAdmin\Controllers\Table\DeleteController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$table' => '%table%',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Table\DropColumnConfirmationController::class => [
            'class' => PhpMyAdmin\Controllers\Table\DropColumnConfirmationController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$table' => '%table%',
            ],
        ],
        PhpMyAdmin\Controllers\Table\DropColumnController::class => [
            'class' => PhpMyAdmin\Controllers\Table\DropColumnController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$table' => '%table%',
                '$dbi' => '@dbi',
                '$flash' => '@flash',
                '$relationCleanup' => '@relation_cleanup',
            ],
        ],
        PhpMyAdmin\Controllers\Table\ExportController::class => [
            'class' => PhpMyAdmin\Controllers\Table\ExportController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$table' => '%table%',
                '$export' => '@export_options',
            ],
        ],
        PhpMyAdmin\Controllers\Table\FindReplaceController::class => [
            'class' => PhpMyAdmin\Controllers\Table\FindReplaceController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$table' => '%table%',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Table\GetFieldController::class => [
            'class' => PhpMyAdmin\Controllers\Table\GetFieldController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$table' => '%table%',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Table\GisVisualizationController::class => [
            'class' => PhpMyAdmin\Controllers\Table\GisVisualizationController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$table' => '%table%',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Table\ImportController::class => [
            'class' => PhpMyAdmin\Controllers\Table\ImportController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$table' => '%table%',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Table\IndexesController::class => [
            'class' => PhpMyAdmin\Controllers\Table\IndexesController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$table' => '%table%',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Table\MaintenanceController::class => [
            'class' => PhpMyAdmin\Controllers\Table\MaintenanceController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$table' => '%table%',
                '$model' => '@table_maintenance',
            ],
        ],
        PhpMyAdmin\Controllers\Table\PartitionController::class => [
            'class' => PhpMyAdmin\Controllers\Table\PartitionController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$table' => '%table%',
                '$maintenance' => '@partitioning_maintenance',
            ],
        ],
        PhpMyAdmin\Controllers\Table\OperationsController::class => [
            'class' => PhpMyAdmin\Controllers\Table\OperationsController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$table' => '%table%',
                '$operations' => '@operations',
                '$checkUserPrivileges' => '@check_user_privileges',
                '$relation' => '@relation',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Table\PrivilegesController::class => [
            'class' => PhpMyAdmin\Controllers\Table\PrivilegesController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$table' => '%table%',
                '$privileges' => '@server_privileges',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Table\RecentFavoriteController::class => [
            'class' => PhpMyAdmin\Controllers\Table\RecentFavoriteController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$table' => '%table%',
            ],
        ],
        PhpMyAdmin\Controllers\Table\RelationController::class => [
            'class' => PhpMyAdmin\Controllers\Table\RelationController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$table' => '%table%',
                '$relation' => '@relation',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Table\ReplaceController::class => [
            'class' => PhpMyAdmin\Controllers\Table\ReplaceController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$table' => '%table%',
                '$insertEdit' => '@insert_edit',
                '$transformations' => '@transformations',
                '$relation' => '@relation',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Table\SearchController::class => [
            'class' => PhpMyAdmin\Controllers\Table\SearchController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$table' => '%table%',
                '$search' => '@table_search',
                '$relation' => '@relation',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Table\SqlController::class => [
            'class' => PhpMyAdmin\Controllers\Table\SqlController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$table' => '%table%',
                '$sqlQueryForm' => '@sql_query_form',
            ],
        ],
        PhpMyAdmin\Controllers\Table\Structure\AddIndexController::class => [
            'class' => PhpMyAdmin\Controllers\Table\Structure\AddIndexController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$table' => '%table%',
                '$dbi' => '@dbi',
                '$structureController' => '@' . PhpMyAdmin\Controllers\Table\StructureController::class,
            ],
        ],
        PhpMyAdmin\Controllers\Table\Structure\AddKeyController::class => [
            'class' => PhpMyAdmin\Controllers\Table\Structure\AddKeyController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$table' => '%table%',
                '$sqlController' => '@' . PhpMyAdmin\Controllers\SqlController::class,
                '$structureController' => '@' . PhpMyAdmin\Controllers\Table\StructureController::class,
            ],
        ],
        PhpMyAdmin\Controllers\Table\Structure\BrowseController::class => [
            'class' => PhpMyAdmin\Controllers\Table\Structure\BrowseController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$table' => '%table%',
                '$sql' => '@sql',
            ],
        ],
        PhpMyAdmin\Controllers\Table\Structure\CentralColumnsAddController::class => [
            'class' => PhpMyAdmin\Controllers\Table\Structure\CentralColumnsAddController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$table' => '%table%',
                '$centralColumns' => '@central_columns',
                '$structureController' => '@' . PhpMyAdmin\Controllers\Table\StructureController::class,
            ],
        ],
        PhpMyAdmin\Controllers\Table\Structure\CentralColumnsRemoveController::class => [
            'class' => PhpMyAdmin\Controllers\Table\Structure\CentralColumnsRemoveController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$table' => '%table%',
                '$centralColumns' => '@central_columns',
                '$structureController' => '@' . PhpMyAdmin\Controllers\Table\StructureController::class,
            ],
        ],
        PhpMyAdmin\Controllers\Table\Structure\ChangeController::class => [
            'class' => PhpMyAdmin\Controllers\Table\Structure\ChangeController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$table' => '%table%',
                '$relation' => '@relation',
                '$transformations' => '@transformations',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Table\Structure\FulltextController::class => [
            'class' => PhpMyAdmin\Controllers\Table\Structure\FulltextController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$table' => '%table%',
                '$dbi' => '@dbi',
                '$structureController' => '@' . PhpMyAdmin\Controllers\Table\StructureController::class,
            ],
        ],
        PhpMyAdmin\Controllers\Table\Structure\MoveColumnsController::class => [
            'class' => PhpMyAdmin\Controllers\Table\Structure\MoveColumnsController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$table' => '%table%',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Table\Structure\PartitioningController::class => [
            'class' => PhpMyAdmin\Controllers\Table\Structure\PartitioningController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$table' => '%table%',
                '$dbi' => '@dbi',
                '$createAddField' => '@create_add_field',
                '$structureController' => '@' . PhpMyAdmin\Controllers\Table\StructureController::class,
            ],
        ],
        PhpMyAdmin\Controllers\Table\Structure\PrimaryController::class => [
            'class' => PhpMyAdmin\Controllers\Table\Structure\PrimaryController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$table' => '%table%',
                '$dbi' => '@dbi',
                '$structureController' => '@' . PhpMyAdmin\Controllers\Table\StructureController::class,
            ],
        ],
        PhpMyAdmin\Controllers\Table\Structure\ReservedWordCheckController::class => [
            'class' => PhpMyAdmin\Controllers\Table\Structure\ReservedWordCheckController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$table' => '%table%',
            ],
        ],
        PhpMyAdmin\Controllers\Table\Structure\SaveController::class => [
            'class' => PhpMyAdmin\Controllers\Table\Structure\SaveController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$table' => '%table%',
                '$relation' => '@relation',
                '$transformations' => '@transformations',
                '$dbi' => '@dbi',
                '$structureController' => '@' . PhpMyAdmin\Controllers\Table\StructureController::class,
            ],
        ],
        PhpMyAdmin\Controllers\Table\Structure\SpatialController::class => [
            'class' => PhpMyAdmin\Controllers\Table\Structure\SpatialController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$table' => '%table%',
                '$dbi' => '@dbi',
                '$structureController' => '@' . PhpMyAdmin\Controllers\Table\StructureController::class,
            ],
        ],
        PhpMyAdmin\Controllers\Table\Structure\UniqueController::class => [
            'class' => PhpMyAdmin\Controllers\Table\Structure\UniqueController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$table' => '%table%',
                '$dbi' => '@dbi',
                '$structureController' => '@' . PhpMyAdmin\Controllers\Table\StructureController::class,
            ],
        ],
        PhpMyAdmin\Controllers\Table\StructureController::class => [
            'class' => PhpMyAdmin\Controllers\Table\StructureController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$table' => '%table%',
                '$relation' => '@relation',
                '$transformations' => '@transformations',
                '$createAddField' => '@create_add_field',
                '$relationCleanup' => '@relation_cleanup',
                '$dbi' => '@dbi',
                '$flash' => '@flash',
            ],
        ],
        PhpMyAdmin\Controllers\Table\TrackingController::class => [
            'class' => PhpMyAdmin\Controllers\Table\TrackingController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$table' => '%table%',
                '$tracking' => '@tracking',
            ],
        ],
        PhpMyAdmin\Controllers\Table\TriggersController::class => [
            'class' => PhpMyAdmin\Controllers\Table\TriggersController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$table' => '%table%',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\Table\ZoomSearchController::class => [
            'class' => PhpMyAdmin\Controllers\Table\ZoomSearchController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$db' => '%db%',
                '$table' => '%table%',
                '$search' => '@table_search',
                '$relation' => '@relation',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\TableController::class => [
            'class' => PhpMyAdmin\Controllers\TableController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\ThemesController::class => [
            'class' => PhpMyAdmin\Controllers\ThemesController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$themeManager' => '@theme_manager',
            ],
        ],
        PhpMyAdmin\Controllers\Transformation\OverviewController::class => [
            'class' => PhpMyAdmin\Controllers\Transformation\OverviewController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$transformations' => '@transformations',
            ],
        ],
        PhpMyAdmin\Controllers\Transformation\WrapperController::class => [
            'class' => PhpMyAdmin\Controllers\Transformation\WrapperController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$transformations' => '@transformations',
                '$relation' => '@relation',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\UserPasswordController::class => [
            'class' => PhpMyAdmin\Controllers\UserPasswordController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$userPassword' => '@user_password',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\VersionCheckController::class => [
            'class' => PhpMyAdmin\Controllers\VersionCheckController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
            ],
        ],
        PhpMyAdmin\Controllers\View\CreateController::class => [
            'class' => PhpMyAdmin\Controllers\View\CreateController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$dbi' => '@dbi',
            ],
        ],
        PhpMyAdmin\Controllers\View\OperationsController::class => [
            'class' => PhpMyAdmin\Controllers\View\OperationsController::class,
            'arguments' => [
                '$response' => '@response',
                '$template' => '@template',
                '$operations' => '@operations',
                '$dbi' => '@dbi',
            ],
        ],
    ],
];
