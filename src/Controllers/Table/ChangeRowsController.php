<?php

declare(strict_types=1);

namespace PhpMyAdmin\Controllers\Table;

use PhpMyAdmin\Controllers\AbstractController;
use PhpMyAdmin\Http\ServerRequest;
use PhpMyAdmin\ResponseRenderer;
use PhpMyAdmin\Template;
use PhpMyAdmin\Url;

use function __;
use function array_values;
use function is_array;

final class ChangeRowsController extends AbstractController
{
    public function __construct(
        ResponseRenderer $response,
        Template $template,
        private ChangeController $changeController,
    ) {
        parent::__construct($response, $template);
    }

    public function __invoke(ServerRequest $request): void
    {
        $GLOBALS['active_page'] ??= null;
        $GLOBALS['where_clause'] ??= null;

        $rowsToDelete = $request->getParsedBodyParam('rows_to_delete');
        $rowsToDeleteIsValid = ! isset($rowsToDelete) || ! is_array($rowsToDelete);

        if ($rowsToDeleteIsValid && $request->getParsedBodyParam('goto') !== null) {
            $this->response->setRequestStatus(false);
            $this->response->addJSON('message', __('No row selected.'));

            return;
        }

        // As we got the rows to be edited from the
        // 'rows_to_delete' checkbox, we use the index of it as the
        // indicating WHERE clause. Then we build the array which is used
        // for the /table/change script.
        $GLOBALS['where_clause'] = [];
        if ($rowsToDeleteIsValid) {
            $GLOBALS['where_clause'] = array_values($rowsToDelete);
        }

        $GLOBALS['active_page'] = Url::getFromRoute('/table/change');

        ($this->changeController)($request);
    }
}
