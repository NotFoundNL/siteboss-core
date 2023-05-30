<?php

namespace NotFound\Framework\Http\Controllers\Forwards;

use Illuminate\Support\Facades\Http;
use NotFound\Layout\Elements\LayoutBreadcrumb;
use NotFound\Layout\Elements\LayoutButton;
use NotFound\Layout\Elements\LayoutForm;
use NotFound\Layout\Elements\LayoutNavigationItem;
use NotFound\Layout\Elements\LayoutPage;
use NotFound\Layout\Elements\LayoutTable;
use NotFound\Layout\Elements\LayoutTableColumn;
use NotFound\Layout\Elements\LayoutTableHeader;
use NotFound\Layout\Elements\LayoutTableRow;
use NotFound\Layout\Elements\LayoutWidget;

class ForwardsController extends \NotFound\Framework\Http\Controllers\Controller
{
    public function readAll()
    {
        $page = new LayoutPage(title: 'Forward domeinnamen');
        $widget = new LayoutWidget('Domeinen');
        $table = new LayoutTable(sort: false);

        $table->addHeader(new LayoutTableHeader(title: 'Domeinnaam'));
        $table->addHeader(new LayoutTableHeader(title: 'WWW-variant actief'));

        $response = Http::withHeaders([
            'token' => 'B58337F3-E2E4-4003-AB7A-421B2A71D091',
        ])->acceptJson()->get('https://forward.siteboss.nl/api/v1/domain?token=', [
            'token' => 'B58337F3-E2E4-4003-AB7A-421B2A71D091',
        ]);

        if ($response->successful()) {
            foreach (json_decode($response->body())->domains as $domain) {
                $tableRow = new LayoutTableRow(id: $domain->id, link: '/forwards/www/');
                $tableRow->addColumn(new LayoutTableColumn(value: $domain->domain));
                $tableRow->addColumn(new LayoutTableColumn(value: $domain->www, type: 'checkbox'));

                $table->addRow($tableRow);
            }
        }

        return $page
            ->addContent(
                $widget->addContent($table)
            )
            ->build();
    }

    public function getOptions()
    {
        $page = new LayoutPage(title: 'Forward domeinnamen');
        $widget = new LayoutWidget('Domeinen');

        $widget->addContent(new LayoutNavigationItem(title: 'Domeinen', link: '/app/forwards/domains/'));
        $widget->addContent(new LayoutNavigationItem(title: 'Regels', link: '/app/forwards/rules/'));

        return $page
            ->addContent(
                $widget
            )
            ->build();
    }

    public function readOne()
    {
        $page = new LayoutPage('Regel bewerken');

        $breadcrumbs = new LayoutBreadcrumb();
        $breadcrumbs->addItem(title: 'Home', link: '/');
        $breadcrumbs->addItem(title: 'Forwards', link: '/app/forwards/');
        $breadcrumbs->addItem(title: 'Regels');
        $page->addContent($breadcrumbs);

        $widget = new LayoutWidget('Regel');

        $form = new LayoutForm(config('app.api_prefix').'/app/');

        $button = new LayoutButton('Sla regel op');
        $form->items = [];
        $form->items[] = json_decode('{
            "type": "Text",
            "internal": "test",
            "properties": {
                "title": "Introductie",
                "required": true
            },
            "data": {
                "value": "Hiero"
            }
        }');
        $form->addContent($button);

        $page->addContent(
            $widget->addContent($form)
        );

        return $page->build();
    }
}
