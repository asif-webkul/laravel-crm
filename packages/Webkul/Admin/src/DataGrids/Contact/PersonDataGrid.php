<?php

namespace Webkul\Admin\DataGrids\Contact;

use Webkul\UI\DataGrid\DataGrid;
use Illuminate\Support\Facades\DB;

class PersonDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     *
     * @return void
     */
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('persons')
            ->addSelect(
                'persons.id',
                'persons.name as person_name',
                'persons.emails',
                'persons.contact_numbers',
                'organizations.name as organization'
            )
            ->leftJoin('organizations', 'persons.organization_id', '=', 'organizations.id');

        $this->addFilter('id', 'persons.id');
        $this->addFilter('person_name', 'persons.name');
        $this->addFilter('organization', 'organizations.id');

        $this->setQueryBuilder($queryBuilder);
    }

    /**
     * Add columns.
     *
     * @return void
     */
    public function addColumns()
    {
        $this->addColumn([
            'index'      => 'id',
            'label'      => trans('admin::app.datagrid.id'),
            'type'       => 'hidden',
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'             => 'person_name',
            'label'             => trans('admin::app.datagrid.name'),
            'type'              => 'string',
            'sortable'          => true,
        ]);

        $this->addColumn([
            'index'             => 'emails',
            'label'             => trans('admin::app.datagrid.emails'),
            'type'              => 'string',
            'sortable'          => false,
            'closure'           => function ($row) {
                $emails = json_decode($row->emails, true);

                if ($emails) {
                    return collect($emails)->pluck('value')->join(', ');
                }
            },
        ]);

        $this->addColumn([
            'index'             => 'contact_numbers',
            'label'             => trans('admin::app.datagrid.contact_numbers'),
            'type'              => 'string',
            'sortable'          => false,
            'closure'           => function ($row) {
                $contactNumbers = json_decode($row->contact_numbers, true);

                if ($contactNumbers) {
                    return collect($contactNumbers)->pluck('value')->join(', ');
                }
            },
        ]);

        $this->addColumn([
            'index'              => 'organization',
            'label'              => trans('admin::app.datagrid.organization_name'),
            'type'               => 'dropdown',
            'dropdown_options'   => app(\Webkul\Contact\Repositories\OrganizationRepository::class)->get(['id as value', 'name as label'])->toArray(),
            'sortable'           => false,
        ]);
    }

    /**
     * Prepare actions.
     *
     * @return void
     */
    public function prepareActions()
    {
        $this->addAction([
            'title'  => trans('ui::app.datagrid.edit'),
            'method' => 'GET',
            'route'  => 'admin.contacts.persons.edit',
            'icon'   => 'pencil-icon',
        ]);

        $this->addAction([
            'title'        => trans('ui::app.datagrid.delete'),
            'method'       => 'DELETE',
            'route'        => 'admin.contacts.persons.delete',
            'confirm_text' => trans('ui::app.datagrid.massaction.delete', ['resource' => trans('admin::app.contacts.persons.person')]),
            'icon'         => 'trash-icon',
        ]);
    }

    /**
     * Prepare mass actions.
     *
     * @return void
     */
    public function prepareMassActions()
    {
        $this->addMassAction([
            'type'   => 'delete',
            'label'  => trans('ui::app.datagrid.delete'),
            'action' => route('admin.contacts.persons.mass_delete'),
            'method' => 'PUT',
        ]);
    }
}
