@extends('layouts.admin')

@section('title')
stiahnutia |
@parent
@stop

@section('content')


<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Stiahnutia</h1>

        @if (Session::has('message'))
            <div class="alert alert-info alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>{!! Session::get('message') !!}</div>
        @endif

    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <!-- /.panel-heading -->
            <div class="panel-body">
            	<table class="table table-hover datatable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>ID diela</th>
                            <th>Názov diela</th>
                            <th>Type</th>
                            <th>Email</th>
                            <th>Dátum</th>
                            <th>Akcie</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->


@stop

{{-- script --}}
@section('script')

<script type="text/javascript" src="https://cdn.datatables.net/v/bs/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.js"></script>

<script>

    $(document).ready(function(){

        // $.fn.dataTable.moment( 'DD.MM.YYYY HH:mm' );

        var table = $('.datatable').dataTable({
            // "order" : [[ 0, "desc" ]],
            // dom: 'Blfrtip',
            "processing": true,
            "serverSide": true,
            "ajax": "",
            "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "Všetky"] ],
            "columns": [
                {data: 'id', name: 'downloads.id'},
                {data: 'item.id', name: 'item.id' },
                {data: 'item.title', name: 'item.title' },
                {data: 'type', name: 'downloads.type' },
                {data: 'email', name: 'downloads.email' },
                {data: 'created_at', name: 'downloads.created_at' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false, className: "actions text-right" }
            ],
            dom:
                "<'row'<'col-sm-3'l><'col-sm-6 text-center'B><'col-sm-3'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            buttons: [
                    {
                        extend: 'csv',
                        text: 'export všetko do CSV'
                    },
                    {
                        extend: 'csv',
                        text: 'export zvolených do CSV',
                        exportOptions: {
                            modifier: {
                                selected: true
                            }
                        }
                    }
                ],
            select: true,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Slovak.json"
            },
            "fnDrawCallback": function(oSettings) {
                $(".action").prependTo(".dt-buttons");
                initFeatures();
            }
        });

    });

</script>
@stop