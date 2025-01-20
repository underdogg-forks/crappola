{{-- @if (Utils::isSelfHost())
	@foreach(Module::getOrdered() as $module)
	    @if(View::exists($module->getLowerName() . '::extend.list'))
	        @includeIf($module->getLowerName() . '::extend.list')
	    @endif
	@endforeach
@endif --}}

{!! Former::open('/clients/bulk')
		->addClass('listForm_client') !!}

<div style="display:none">
    {!! Former::text('action')->id('action_client') !!}
    {!! Former::text('public_id')->id('public_id_client') !!}
    {!! Former::text('datatable')->value('true') !!}
</div>

<div class="pull-left">
    {{--@if (in_array($entityType, [ENTITY_TASK, ENTITY_EXPENSE, ENTITY_PRODUCT, ENTITY_PROJECT]))
        @can('createEntity', 'invoice')
            {!! Button::primary(trans('texts.invoice'))->withAttributes(['class'=>'invoice', 'onclick' =>'submitForm_'.$entityType.'("invoice")'])->appendIcon(Icon::create('check')) !!}
        @endcan
    @endif--}}

    {!! DropdownButton::normal(trans('texts.archive'))
            ->withContents($datatable->bulkActions())
            ->withAttributes(['class'=>'archive'])
            ->split() !!}
    &nbsp;
    <span id="statusWrapper_client" style="display:none">
		<select class="form-control" style="width: 220px" id="statuses_client" multiple="true">
			@if (count(\App\Models\EntityModel::getStatusesFor('client')))
                <optgroup label="{{ trans('texts.entity_state') }}">
					@foreach (\App\Models\EntityModel::getStatesFor('client') as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
				</optgroup>
                <optgroup label="{{ trans('texts.status') }}">
					@foreach (\App\Models\EntityModel::getStatusesFor('client') as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
				</optgroup>
            @else
                @foreach (\App\Models\EntityModel::getStatesFor('client') as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            @endif
		</select>
	</span>
    &nbsp;
    <span class="well well-sm" id="sum_column_client"
          style="display:none;padding-left:12px;padding-right:12px;"></span>
</div>

<div id="top_right_buttons" class="pull-right">
    <input id="tableFilter_client" type="text"
           style="width:180px;margin-right:17px;background-color: white !important"
           class="form-control pull-left" placeholder="{{ trans('texts.filter') }}"
           value="{{ request()->get('filter') }}"/>

    @if (Utils::isSelfHost())
        @stack('top_right_buttons')
    @endif

    @if (Auth::user()->can('createEntity', 'client') && empty($vendorId))
        {!! Button::primary(mtrans('client', "new_client"))
            ->asLinkTo(url(
                (in_array('client', [ENTITY_PROPOSAL_SNIPPET, ENTITY_PROPOSAL_CATEGORY, ENTITY_PROPOSAL_TEMPLATE]) ? str_replace('_', 's/', Utils::pluralizeEntityType('client')) : Utils::pluralizeEntityType('client')) .
                '/create/' . (isset($clientId) ? ($clientId . (isset($projectId) ? '/' . $projectId : '')) : '')
            ))
            ->appendIcon(Icon::create('plus-sign')) !!}
    @endif

</div>

{{--{!! DataTable::query()
	->addColumn(Utils::trans($datatable->columnFields(), $datatable->entityType))
	->setUrl(empty($url) ? url('api/' . Utils::pluralizeEntityType('client')) : $url)
	->setCustomValues('entityType', Utils::pluralizeEntityType('client'))
	->setCustomValues('clientId', isset($clientId) && $clientId && empty($projectId))
	->setOptions('sPaginationType', 'bootstrap')
    ->setOptions('aaSorting', [[isset($clientId) ? ($datatable->sortCol-1) : $datatable->sortCol, 'desc']])
	->render('datatable') !!}--}}

<table id="bOT3Ux93">
</table>


{{--<div id="DataTables_Table_0_wrapper" class="dataTables_wrapper form-inline no-footer">
    <table class="table table-striped data-table bOT3Ux93 dataTable no-footer" id="DataTables_Table_0"
           role="grid" aria-describedby="DataTables_Table_0_info">
        <colgroup>
            <col class="con0">
            <col class="con1">
            <col class="con2">
            <col class="con3">
            <col class="con4">
            <col class="con5">
            <col class="con6">
            <col class="con7">
        </colgroup>
        <thead>
        <tr role="row">
            <th align="center" valign="middle" class="head0 sorting_disabled" style="width:20px"
                rowspan="1" colspan="1" aria-label="

                    ">
                <input type="checkbox" class="selectAll">
            </th>
            <th align="center" valign="middle" class="head1 sorting" tabindex="0"
                aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="
                            Name
                    : activate to sort column ascending">
                Name
            </th>
            <th align="center" valign="middle" class="head2 sorting" tabindex="0"
                aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="
                            Contact
                    : activate to sort column ascending">
                Contact
            </th>
            <th align="center" valign="middle" class="head3 sorting" tabindex="0"
                aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="
                            Email
                    : activate to sort column ascending">
                Email
            </th>
            <th align="center" valign="middle" class="head4 sorting_desc" tabindex="0"
                aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-sort="descending"
                aria-label="
                            Date Created
                    : activate to sort column ascending">
                Date Created
            </th>
            <th align="center" valign="middle" class="head5 sorting" tabindex="0"
                aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="
                            Last Login
                    : activate to sort column ascending">
                Last Login
            </th>
            <th align="center" valign="middle" class="head6 sorting" tabindex="0"
                aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="
                            Balance
                    : activate to sort column ascending">
                Balance
            </th>
            <th align="center" valign="middle" class="head7 sorting_disabled" rowspan="1" colspan="1"
                aria-label="

                    ">

            </th>
        </tr>
        </thead>
        <tbody>
        <tr role="row" class="odd">
            <td><input type="checkbox" name="ids[]" value="62"></td>
            <td><a href="/clients/62">Prof. Dina Bosco Sr.</a></td>
            <td><a href="/clients/62">Cleveland Homenick</a></td>
            <td><a href="/clients/62">hwaters@example.net</a></td>
            <td>May 18, 2019</td>
            <td></td>
            <td>$0.00</td>
            <td>
                <center style="min-width:100px">
                    <div class="tr-status"></div>
                    <div class="btn-group tr-action" style="height:auto;display:none">
                        <button type="button" class="btn btn-xs btn-default dropdown-toggle"
                                data-toggle="dropdown" style="width:100px">
                            Select <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="/clients/62/edit">Edit Client</a></li>
                            <li class="divider"></li>
                            <li><a href="/tasks/create/62">New Task</a></li>
                            <li><a href="/invoices/create/62">New Invoice</a></li>
                            <li><a href="/quotes/create/62">New Quote</a></li>
                            <li class="divider"></li>
                            <li><a href="/payments/create/62">Enter Payment</a></li>
                            <li><a href="/credits/create/62">Enter Credit</a></li>
                            <li><a href="/expenses/create/62">Enter Expense</a></li>
                            <li class="divider"></li>
                            <li><a href="javascript:submitForm_client(&#39;archive&#39;, 62)">Archive
                                    Client</a></li>
                            <li><a href="javascript:submitForm_client(&#39;delete&#39;, 62)">Delete
                                    Client</a></li>
                        </ul>
                    </div>
                </center>
            </td>
        </tr>
        <tr role="row" class="even">
            <td><input type="checkbox" name="ids[]" value="61"></td>
            <td><a href="/clients/61">Dr. Lou McDermott DVM</a></td>
            <td><a href="/clients/61">Kathryne Kohler</a></td>
            <td><a href="/clients/61">george.predovic@example.org</a></td>
            <td>May 18, 2019</td>
            <td></td>
            <td>$0.00</td>
            <td>
                <center style="min-width:100px">
                    <div class="tr-status"></div>
                    <div class="btn-group tr-action" style="height:auto;display:none">
                        <button type="button" class="btn btn-xs btn-default dropdown-toggle"
                                data-toggle="dropdown" style="width:100px">
                            Select <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="/clients/61/edit">Edit Client</a></li>
                            <li class="divider"></li>
                            <li><a href="/tasks/create/61">New Task</a></li>
                            <li><a href="/invoices/create/61">New Invoice</a></li>
                            <li><a href="/quotes/create/61">New Quote</a></li>
                            <li class="divider"></li>
                            <li><a href="/payments/create/61">Enter Payment</a></li>
                            <li><a href="/credits/create/61">Enter Credit</a></li>
                            <li><a href="/expenses/create/61">Enter Expense</a></li>
                            <li class="divider"></li>
                            <li><a href="javascript:submitForm_client(&#39;archive&#39;, 61)">Archive
                                    Client</a></li>
                            <li><a href="javascript:submitForm_client(&#39;delete&#39;, 61)">Delete
                                    Client</a></li>
                        </ul>
                    </div>
                </center>
            </td>
        </tr>
        <tr role="row" class="odd">
            <td><input type="checkbox" name="ids[]" value="60"></td>
            <td><a href="/clients/60">Dr. Mose O'Keefe IV</a></td>
            <td><a href="/clients/60">Peyton Lemke</a></td>
            <td><a href="/clients/60">drohan@example.net</a></td>
            <td>May 18, 2019</td>
            <td></td>
            <td>$0.00</td>
            <td>
                <center style="min-width:100px">
                    <div class="tr-status" style="display: block;"></div>
                    <div class="btn-group tr-action" style="height: auto; display: none;">
                        <button type="button" class="btn btn-xs btn-default dropdown-toggle"
                                data-toggle="dropdown" style="width:100px">
                            Select <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="/clients/60/edit">Edit Client</a></li>
                            <li class="divider"></li>
                            <li><a href="/tasks/create/60">New Task</a></li>
                            <li><a href="/invoices/create/60">New Invoice</a></li>
                            <li><a href="/quotes/create/60">New Quote</a></li>
                            <li class="divider"></li>
                            <li><a href="/payments/create/60">Enter Payment</a></li>
                            <li><a href="/credits/create/60">Enter Credit</a></li>
                            <li><a href="/expenses/create/60">Enter Expense</a></li>
                            <li class="divider"></li>
                            <li><a href="javascript:submitForm_client(&#39;archive&#39;, 60)">Archive
                                    Client</a></li>
                            <li><a href="javascript:submitForm_client(&#39;delete&#39;, 60)">Delete
                                    Client</a></li>
                        </ul>
                    </div>
                </center>
            </td>
        </tr>
        <tr role="row" class="even">
            <td><input type="checkbox" name="ids[]" value="59"></td>
            <td><a href="/clients/59">Margaret Shields</a></td>
            <td><a href="/clients/59">Effie Prohaska</a></td>
            <td><a href="/clients/59">urolfson@example.net</a></td>
            <td>May 18, 2019</td>
            <td></td>
            <td>$0.00</td>
            <td>
                <center style="min-width:100px">
                    <div class="tr-status" style="display: none;"></div>
                    <div class="btn-group tr-action" style="height: auto; display: inline-block;">
                        <button type="button" class="btn btn-xs btn-default dropdown-toggle"
                                data-toggle="dropdown" style="width:100px">
                            Select <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="/clients/59/edit">Edit Client</a></li>
                            <li class="divider"></li>
                            <li><a href="/tasks/create/59">New Task</a></li>
                            <li><a href="/invoices/create/59">New Invoice</a></li>
                            <li><a href="/quotes/create/59">New Quote</a></li>
                            <li class="divider"></li>
                            <li><a href="/payments/create/59">Enter Payment</a></li>
                            <li><a href="/credits/create/59">Enter Credit</a></li>
                            <li><a href="/expenses/create/59">Enter Expense</a></li>
                            <li class="divider"></li>
                            <li><a href="javascript:submitForm_client(&#39;archive&#39;, 59)">Archive
                                    Client</a></li>
                            <li><a href="javascript:submitForm_client(&#39;delete&#39;, 59)">Delete
                                    Client</a></li>
                        </ul>
                    </div>
                </center>
            </td>
        </tr>
        <tr role="row" class="odd">
            <td><input type="checkbox" name="ids[]" value="58"></td>
            <td><a href="/clients/58">Rafaela Ratke Sr.</a></td>
            <td><a href="/clients/58">Rodrigo Gutmann</a></td>
            <td><a href="/clients/58">kyle.bauch@example.org</a></td>
            <td>May 18, 2019</td>
            <td></td>
            <td>$0.00</td>
            <td>
                <center style="min-width:100px">
                    <div class="tr-status" style="display: block;"></div>
                    <div class="btn-group tr-action" style="height: auto; display: none;">
                        <button type="button" class="btn btn-xs btn-default dropdown-toggle"
                                data-toggle="dropdown" style="width:100px">
                            Select <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="/clients/58/edit">Edit Client</a></li>
                            <li class="divider"></li>
                            <li><a href="/tasks/create/58">New Task</a></li>
                            <li><a href="/invoices/create/58">New Invoice</a></li>
                            <li><a href="/quotes/create/58">New Quote</a></li>
                            <li class="divider"></li>
                            <li><a href="/payments/create/58">Enter Payment</a></li>
                            <li><a href="/credits/create/58">Enter Credit</a></li>
                            <li><a href="/expenses/create/58">Enter Expense</a></li>
                            <li class="divider"></li>
                            <li><a href="javascript:submitForm_client(&#39;archive&#39;, 58)">Archive
                                    Client</a></li>
                            <li><a href="javascript:submitForm_client(&#39;delete&#39;, 58)">Delete
                                    Client</a></li>
                        </ul>
                    </div>
                </center>
            </td>
        </tr>
        <tr role="row" class="even">
            <td><input type="checkbox" name="ids[]" value="57"></td>
            <td><a href="/clients/57">Austyn Borer</a></td>
            <td><a href="/clients/57">Crystal Cole</a></td>
            <td><a href="/clients/57">julian.little@example.org</a></td>
            <td>May 18, 2019</td>
            <td></td>
            <td>$0.00</td>
            <td>
                <center style="min-width:100px">
                    <div class="tr-status" style="display: block;"></div>
                    <div class="btn-group tr-action" style="height: auto; display: none;">
                        <button type="button" class="btn btn-xs btn-default dropdown-toggle"
                                data-toggle="dropdown" style="width:100px">
                            Select <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="/clients/57/edit">Edit Client</a></li>
                            <li class="divider"></li>
                            <li><a href="/tasks/create/57">New Task</a></li>
                            <li><a href="/invoices/create/57">New Invoice</a></li>
                            <li><a href="/quotes/create/57">New Quote</a></li>
                            <li class="divider"></li>
                            <li><a href="/payments/create/57">Enter Payment</a></li>
                            <li><a href="/credits/create/57">Enter Credit</a></li>
                            <li><a href="/expenses/create/57">Enter Expense</a></li>
                            <li class="divider"></li>
                            <li><a href="javascript:submitForm_client(&#39;archive&#39;, 57)">Archive
                                    Client</a></li>
                            <li><a href="javascript:submitForm_client(&#39;delete&#39;, 57)">Delete
                                    Client</a></li>
                        </ul>
                    </div>
                </center>
            </td>
        </tr>
        <tr role="row" class="odd">
            <td><input type="checkbox" name="ids[]" value="56"></td>
            <td><a href="/clients/56">Nia Quigley</a></td>
            <td><a href="/clients/56">Jonathan Jones</a></td>
            <td><a href="/clients/56">luz36@example.net</a></td>
            <td>May 18, 2019</td>
            <td></td>
            <td>$0.00</td>
            <td>
                <center style="min-width:100px">
                    <div class="tr-status"></div>
                    <div class="btn-group tr-action" style="height:auto;display:none">
                        <button type="button" class="btn btn-xs btn-default dropdown-toggle"
                                data-toggle="dropdown" style="width:100px">
                            Select <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="/clients/56/edit">Edit Client</a></li>
                            <li class="divider"></li>
                            <li><a href="/tasks/create/56">New Task</a></li>
                            <li><a href="/invoices/create/56">New Invoice</a></li>
                            <li><a href="/quotes/create/56">New Quote</a></li>
                            <li class="divider"></li>
                            <li><a href="/payments/create/56">Enter Payment</a></li>
                            <li><a href="/credits/create/56">Enter Credit</a></li>
                            <li><a href="/expenses/create/56">Enter Expense</a></li>
                            <li class="divider"></li>
                            <li><a href="javascript:submitForm_client(&#39;archive&#39;, 56)">Archive
                                    Client</a></li>
                            <li><a href="javascript:submitForm_client(&#39;delete&#39;, 56)">Delete
                                    Client</a></li>
                        </ul>
                    </div>
                </center>
            </td>
        </tr>
        <tr role="row" class="even">
            <td><input type="checkbox" name="ids[]" value="55"></td>
            <td><a href="/clients/55">Herta Jones</a></td>
            <td><a href="/clients/55">Grace Torphy</a></td>
            <td><a href="/clients/55">fvonrueden@example.com</a></td>
            <td>May 18, 2019</td>
            <td></td>
            <td>$0.00</td>
            <td>
                <center style="min-width:100px">
                    <div class="tr-status"></div>
                    <div class="btn-group tr-action" style="height:auto;display:none">
                        <button type="button" class="btn btn-xs btn-default dropdown-toggle"
                                data-toggle="dropdown" style="width:100px">
                            Select <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="/clients/55/edit">Edit Client</a></li>
                            <li class="divider"></li>
                            <li><a href="/tasks/create/55">New Task</a></li>
                            <li><a href="/invoices/create/55">New Invoice</a></li>
                            <li><a href="/quotes/create/55">New Quote</a></li>
                            <li class="divider"></li>
                            <li><a href="/payments/create/55">Enter Payment</a></li>
                            <li><a href="/credits/create/55">Enter Credit</a></li>
                            <li><a href="/expenses/create/55">Enter Expense</a></li>
                            <li class="divider"></li>
                            <li><a href="javascript:submitForm_client(&#39;archive&#39;, 55)">Archive
                                    Client</a></li>
                            <li><a href="javascript:submitForm_client(&#39;delete&#39;, 55)">Delete
                                    Client</a></li>
                        </ul>
                    </div>
                </center>
            </td>
        </tr>
        <tr role="row" class="odd">
            <td><input type="checkbox" name="ids[]" value="54"></td>
            <td><a href="/clients/54">Della Boyle Sr.</a></td>
            <td><a href="/clients/54">Freeman Hodkiewicz</a></td>
            <td><a href="/clients/54">sheathcote@example.com</a></td>
            <td>May 18, 2019</td>
            <td></td>
            <td>$0.00</td>
            <td>
                <center style="min-width:100px">
                    <div class="tr-status"></div>
                    <div class="btn-group tr-action" style="height:auto;display:none">
                        <button type="button" class="btn btn-xs btn-default dropdown-toggle"
                                data-toggle="dropdown" style="width:100px">
                            Select <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="/clients/54/edit">Edit Client</a></li>
                            <li class="divider"></li>
                            <li><a href="/tasks/create/54">New Task</a></li>
                            <li><a href="/invoices/create/54">New Invoice</a></li>
                            <li><a href="/quotes/create/54">New Quote</a></li>
                            <li class="divider"></li>
                            <li><a href="/payments/create/54">Enter Payment</a></li>
                            <li><a href="/credits/create/54">Enter Credit</a></li>
                            <li><a href="/expenses/create/54">Enter Expense</a></li>
                            <li class="divider"></li>
                            <li><a href="javascript:submitForm_client(&#39;archive&#39;, 54)">Archive
                                    Client</a></li>
                            <li><a href="javascript:submitForm_client(&#39;delete&#39;, 54)">Delete
                                    Client</a></li>
                        </ul>
                    </div>
                </center>
            </td>
        </tr>
        <tr role="row" class="even">
            <td><input type="checkbox" name="ids[]" value="53"></td>
            <td><a href="/clients/53">Clovis Frami</a></td>
            <td><a href="/clients/53">Jay O'Conner</a></td>
            <td><a href="/clients/53">tressie.bogan@example.com</a></td>
            <td>May 18, 2019</td>
            <td></td>
            <td>$0.00</td>
            <td>
                <center style="min-width:100px">
                    <div class="tr-status"></div>
                    <div class="btn-group tr-action" style="height:auto;display:none">
                        <button type="button" class="btn btn-xs btn-default dropdown-toggle"
                                data-toggle="dropdown" style="width:100px">
                            Select <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="/clients/53/edit">Edit Client</a></li>
                            <li class="divider"></li>
                            <li><a href="/tasks/create/53">New Task</a></li>
                            <li><a href="/invoices/create/53">New Invoice</a></li>
                            <li><a href="/quotes/create/53">New Quote</a></li>
                            <li class="divider"></li>
                            <li><a href="/payments/create/53">Enter Payment</a></li>
                            <li><a href="/credits/create/53">Enter Credit</a></li>
                            <li><a href="/expenses/create/53">Enter Expense</a></li>
                            <li class="divider"></li>
                            <li><a href="javascript:submitForm_client(&#39;archive&#39;, 53)">Archive
                                    Client</a></li>
                            <li><a href="javascript:submitForm_client(&#39;delete&#39;, 53)">Delete
                                    Client</a></li>
                        </ul>
                    </div>
                </center>
            </td>
        </tr>
        </tbody>
    </table>
    <div class="row-fluid">
        <div class="span6 dt-left">
            <div class="dataTables_info" id="DataTables_Table_0_info" role="status" aria-live="polite">
                Showing 1 to 10 of 62 entries
            </div>
        </div>
        <div class="span6 dt-right">
            <div class="dataTables_paginate paging_bootstrap pagination"
                 id="DataTables_Table_0_paginate">
                <ul class="pagination">
                    <li class="prev disabled"><a href="/clients#">«</a></li>
                    <li class="active"><a href="/clients#">1</a></li>
                    <li><a href="/clients#">2</a></li>
                    <li><a href="/clients#">3</a></li>
                    <li><a href="/clients#">4</a></li>
                    <li><a href="/clients#">5</a></li>
                    <li class="next"><a href="/clients#">»</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="dataTables_length" id="DataTables_Table_0_length"><label><select
                    name="DataTables_Table_0_length" aria-controls="DataTables_Table_0" class="">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select> rows</label></div>
</div>--}}


{!! Former::close() !!}

<style type="text/css">

    @foreach ($datatable->rightAlignIndices() as $index)
		.listForm_client table.dataTable td:nth-child({{ $index }}) {
        text-align: right;
    }

    @endforeach

	@foreach ($datatable->centerAlignIndices() as $index)
		.listForm_client table.dataTable td:nth-child({{ $index }}) {
        text-align: center;
    }
    @endforeach


</style>

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.1.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.1.1/js/buttons.html5.min.js"></script>
    <script type="text/javascript">
        function refreshDatatable_clients() {
            window['dataTable_clients'].api().ajax.reload();
        }

        $('#bOT3Ux93').DataTable({
            stateSave: true,
            stateDuration: 0,
            bAutoWidth: false,
            processing: true,
            serverSide: true,
            ajax: {
                url: '/api/clients',
                dataSrc: 'data'
            },
            columns: [
                { data: 'id' },
                { data: 'name' },
                { data: 'email' }
            ],
            aoColumnDefs: [
                // Disable sorting on the first column
                {
                    'bSortable': false,
                    'aTargets': [0, 7]
                },
                {
                    'sClass': 'right',
                    'aTargets': []
                }
            ],
            fnDrawCallback: function (oSettings) {
                /*if (window.onDatatableReady_clients) {
                    window.onDatatableReady_clients();
                } else if (window.onDatatableReady) {
                    window.onDatatableReady();
                }*/
            },
            stateLoadParams: function (settings, data) {
                // don't save filter to local storage
                data.search.search = "";
                // always start on first page of results
                data.start = 0;
            }
        });
    </script>


    <script type="text/javascript">

        var submittedForm;

        function submitForm_client(action, id) {
            // prevent duplicate form submissions
            if (submittedForm) {
                swal("{{ trans('texts.processing_request') }}")
                return;
            }
            submittedForm = true;

            if (id) {
                $('#public_id_client').val(id);
            }

            if (action == 'delete' || action == 'emailInvoice') {
                sweetConfirm(function () {
                    $('#action_client').val(action);
                    $('form.listForm_client').submit();
                });
            } else {
                $('#action_client').val(action);
                $('form.listForm_client').submit();
            }
        }













        $(function () {
            // Handle datatable filtering
            var tableFilter = '';
            var searchTimeout = false;

            function filterTable_client(val) {
                if (val == tableFilter) {
                    return;
                }
                tableFilter = val;
                var oTable0 = $('.listForm_client .data-table').dataTable();
                oTable0.fnFilter(val);
            }

            $('#tableFilter_client').on('keyup', function () {
                if (searchTimeout) {
                    window.clearTimeout(searchTimeout);
                }
                searchTimeout = setTimeout(function () {
                    filterTable_client($('#tableFilter_client').val());
                }, 500);
            })

            if ($('#tableFilter_client').val()) {
                filterTable_client($('#tableFilter_client').val());
            }

            $('.listForm_client .head0').click(function (event) {
                if (event.target.type !== 'checkbox') {
                    $('.listForm_client .head0 input[type=checkbox]').click();
                }
            });

            // Enable/disable bulk action buttons
            window.onDatatableReady_{{ Utils::pluralizeEntityType('client') }} = function () {
                $(':checkbox').click(function () {
                    setBulkActionsEnabled_client();
                    changeSumLabel();
                });

                $('.listForm_client tbody tr').unbind('click').click(function (event) {
                    if (event.target.type !== 'checkbox' && event.target.type !== 'button' && event.target.tagName.toLowerCase() !== 'a') {
                        $checkbox = $(this).closest('tr').find(':checkbox:not(:disabled)');
                        var checked = $checkbox.prop('checked');
                        $checkbox.prop('checked', !checked);
                        setBulkActionsEnabled_client();
                        changeSumLabel();
                    }
                });

                actionListHandler();
                $('[data-toggle="tooltip"]').tooltip();
            }

            $('.listForm_client .archive, .invoice').prop('disabled', true);
            $('.listForm_client .archive:not(.dropdown-toggle)').click(function () {
                submitForm_client('archive');
            });

            $('.listForm_client .selectAll').click(function () {
                $(this).closest('table').find(':checkbox:not(:disabled)').prop('checked', this.checked);
            });

            function setBulkActionsEnabled_client() {
                var buttonLabel = "{{ trans('texts.archive') }}";
                var count = $('.listForm_client tbody :checkbox:checked').length;
                $('.listForm_client button.archive, .listForm_client button.invoice').prop('disabled', !count);
                if (count) {
                    buttonLabel += ' (' + count + ')';
                }
                $('.listForm_client button.archive').not('.dropdown-toggle').text(buttonLabel);
            }

            function sumColumnVars(currentSum, add) {
                switch ("client") {
                    case "task":
                        if (currentSum == "") {
                            currentSum = "00:00:00";
                        }
                        currentSumMoment = moment.duration(currentSum);
                        addMoment = moment.duration(add);
                        return secondsToTime(currentSumMoment.add(addMoment).asSeconds(), true);
                        break;

                    default:
                        if (currentSum == "") {
                            currentSum = "0"
                        }
                        return (convertStringToNumber(currentSum) + convertStringToNumber(add)).toFixed(2);
                }
            }

            function changeSumLabel() {
                var dTable = $('.listForm_client .data-table').DataTable();
                @if ($datatable->sumColumn() != null)
                @if(in_array('client', [ENTITY_TASK]))
                var sumColumnNodes = dTable.column({{ $datatable->sumColumn() }}).nodes();
                @else
                    sumColumnNodes = dTable.column({{ $datatable->sumColumn() }}).data().toArray();
                @endif
                var sum = 0;
                var cboxArray = dTable.column(0).nodes();

                for (i = 0; i < sumColumnNodes.length; i++) {
                    if (cboxArray[i].firstChild.checked) {
                        var value;
                        @if(in_array('client', [ENTITY_TASK]))
                            value = sumColumnNodes[i].firstChild.innerHTML;
                        @else
                            value = sumColumnNodes[i];
                        @endif
                            sum = sumColumnVars(sum, value);
                    }
                }

                if (sum) {
                    $('#sum_column_client').show().text("{{ trans('texts.total') }}: " + sum)
                } else {
                    $('#sum_column_client').hide();
                }

                @endif
            }

            // Setup state/status filter
            $('#statuses_client').select2({
                placeholder: "{{ trans('texts.status') }}",
                //allowClear: true,
                templateSelection: function (data, container) {
                    if (data.id == 'archived') {
                        $(container).css('color', '#fff');
                        $(container).css('background-color', '#f0ad4e');
                        $(container).css('border-color', '#eea236');
                    } else if (data.id == 'deleted') {
                        $(container).css('color', '#fff');
                        $(container).css('background-color', '#d9534f');
                        $(container).css('border-color', '#d43f3a');
                    }
                    return data.text;
                }
            }).val('{{ session('entity_state_filter:client', STATUS_ACTIVE) . ',' . session('entity_status_filter:client') }}'.split(','))
                .trigger('change')
                .on('change', function () {
                    var filter = $('#statuses_client').val();
                    if (filter) {
                        filter = filter.join(',');
                    } else {
                        filter = '';
                    }
                    var url = '{{ URL::to('set_entity_filter/client') }}' + '/' + filter;
                    $.get(url, function (data) {
                        refreshDatatable_{{ Utils::pluralizeEntityType('client') }}();
                    })
                }).maximizeSelect2Height();

            $('#statusWrapper_client').show();


            @for ($i = 1; $i <= 10; $i++)
            Mousetrap.bind('g {{ $i }}', function (e) {
                var link = $('.data-table').find('tr:nth-child({{ $i }})').find('a').attr('href');
                if (link) {
                    location.href = link;
                }
            });
            @endfor
        });

    </script>

@endpush
