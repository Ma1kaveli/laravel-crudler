<?php

use Crudler\Actions\Constants\ActionConstants;
use Crudler\Controllers\Constants\ControllerConstants;
use Crudler\Repositories\Constants\RepositoryConstants;
use Crudler\Services\Constants\ServiceConstants;

return [
    'repositories' => [
        'show_once_by_id_not_found_message' => RepositoryConstants::SHOW_ONCE_BY_ID_NOT_FOUND_MESSAGE,

        'is_not_unique_message' => RepositoryConstants::IS_NOT_UNIQUE_MESSAGE
    ],

    'services' => [
        'restore' => [
            'not_delete_message' => ServiceConstants::NOT_DELETE_RESTORE_MESSAGE,

            'success_message' => ServiceConstants::SUCCESS_RESTORE_MESSAGE,

            'error_message' => ServiceConstants::ERROR_RESTORE_MESSAGE
        ],

        'delete' => [
            'already_delete_message' => ServiceConstants::ALREADY_DELETE_MESSAGE,

            'success_message' => ServiceConstants::SUCCESS_DELETE_MESSAGE,

            'error_message' => ServiceConstants::ERROR_DELETE_MESSAGE
        ],
    ],

    'actions' => [
        'error_create_message' => ActionConstants::ERROR_CREATE_MESSAGE,

        'error_update_message' => ActionConstants::ERROR_UPDATE_MESSAGE,

        'error_delete_message' => ActionConstants::ERROR_DELETE_MESSAGE,

        'error_restore_message' => ActionConstants::ERROR_RESTORE_MESSAGE
    ],

    'controllers' => [
        'success_create_message' => ControllerConstants::SUCCESS_CREATE_MESSAGE,

        'success_update_message' => ControllerConstants::SUCCESS_UPDATE_MESSAGE,

        'success_delete_message' => ControllerConstants::SUCCESS_DELETE_MESSAGE,

        'success_restore_message' => ControllerConstants::SUCCESS_RESTORE_MESSAGE,
    ]
];
