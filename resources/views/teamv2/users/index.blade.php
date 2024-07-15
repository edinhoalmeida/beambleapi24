@extends('teamv2.layout.layout')

@section('content')

    @include('teamv2.partials._page_title')
    <section class="content">
    
    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif
   
        <div class="card">
              <div class="card-header">
                Filter Shopper
                <div class="btn-group" role="group" id="filter_shopper">
                      <input type="hidden" id="filtered_shop" value="Tous">
                    <button type="button" data-filter="Tous" class="btn btn-default" disabled>Tous</button>
                    <button type="button" data-filter="Non" class="btn btn-default">Non</button>
                    <button type="button" data-filter="Oui" class="btn btn-default">Oui</button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="example1" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>#</th>
                    <th>Surname</th>
                    <th>Name</th>
                    <th>E-mail</th>
                    <th>Client enabled</th>
                    <th>Beamer enabled (Stripe)</th>
                    <th>Shopper enabled</th>
                  </tr>
                  </thead>
                  {{-- <tbody>
                    @foreach($users as $user)
                      <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->surname }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->client_enabled ? '1' : '0'  }}</td>
                        <td>{{ $user->beamer_enabled ? '1' : '0' }}</td>
                        <td>{!! $user->shopper_enabled ? '<span class="float-right badge bg-success">Oui</span>' : '<span class="float-right badge bg-secondary">Non</span>' !!}</td>
                      </tr>
                    @endforeach
                  
                  </tfoot> --}}
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>


</section>
@endsection



@section('footer_scripts')
<script>
const filtered_shop = document.querySelector('#filtered_shop');
var table;
var table_api;

function format_shooper_column(row, data, row_id){
    if(data==1){
      var div_change ='<div class="dropdown"><button class="btn dropdown-toggle" type="button" id="dropdownMenuButton'+ row[0] +'" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="badge bg-success">Oui</span></button><div class="dropdown-menu" aria-labelledby="dropdownMenuButton'+ row[0] +'"  style=""><a class="dropdown-item update_shop" href="#" data-shopstate="0" data-userid="'+ row[0] +'" data-row_id="'+ row_id +'">Disable Shopper</a></div></div>';
      return div_change;
    }
      var div_change ='<div class="dropdown"><button class="btn dropdown-toggle" type="button" id="dropdownMenuButton'+ row[0] +'" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="badge bg-secondary">Non</span></button><div class="dropdown-menu" aria-labelledby="dropdownMenuButton'+ row[0] +'"  style=""><a class="dropdown-item update_shop" href="#" data-shopstate="1" data-userid="'+ row[0] +'" data-row_id="'+ row_id +'">Enable Shopper</a></div></div>';
    return div_change;
}
$(function () {



    table = $("#example1").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      ajax: '/users',
        "columnDefs": [ 
        {
          "targets": 6,
            "render": function ( data, type, row, meta ) {
              return format_shooper_column(row, data, meta.row);
            }
        }
      ],
      initComplete: function () {
        table_api = this.api();
      },
      "buttons": ["csv", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

    $("#filter_shopper button").on('click', function(){
        if($(this).prop('disabled')==true){
          return;
        }
        filtered_shop.value = $(this).data('filter');
        $("#filter_shopper button").prop('disabled', false);
        $(this).prop('disabled', true);
        if(filtered_shop.value=='Tous'){
          table_api
            .column(6)
            .search('.*', true)
            .draw();
        } else {
          table_api
            .column(6)
            .search(filtered_shop.value)
            .draw();
        }
    });


    $('#example1').on( 'click','.update_shop',function (e) {
        e.preventDefault();
        var shopstate = $(this).data('shopstate');
        var userid = $(this).data('userid');
        var row_id = parseInt($(this).data('row_id'));
        var td = $(this).closest('td');
        $.ajax({
          url: "/users-shopper",
          data: {'shopstate': shopstate, 'userid': userid},
          dataType : "json",
          method: "POST"
          }).done(function(rdata) {
              table_api.row(row_id).data(rdata);
              td.html(format_shooper_column([rdata[0]], rdata[6], row_id));
          });
    } );

    // $("#example1").DataTable({
    //   "responsive": true, "lengthChange": false, "autoWidth": false,
    //   "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    // }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');


    // $('#example2').DataTable({
    //   "paging": true,
    //   "lengthChange": false,
    //   "searching": false,
    //   "ordering": true,
    //   "info": true,
    //   "autoWidth": false,
    //   "responsive": true,
    // });
});
</script>

@endsection
