@extends('layouts.app')
@section('content')
    @include('components.navbar')
    @include('components.sidebar')
    <style>
        #cover-spin {
            position: fixed;
            width: 100%;
            left: 0;
            right: 0;
            top: 0;
            bottom: 0;
            background-color: rgba(255, 255, 255, 0.7);
            z-index: 9999;
            display: none;
        }
    </style>
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <main id="main" class="main">
        <div id="cover-spin">
            <div class="position-absolute top-50 start-50 spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        <div class="pagetitle">
          
            <h1>Data Tables</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                    <li class="breadcrumb-item">Tables</li>
                    <li class="breadcrumb-item active">Data</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Data Siswa</h5>
                            <!-- Table with stripped rows -->
                            <div class="row">
                              <div class="col-md-6 mb-2">
                                  <button class="btn btn-success btn-sm"  id="createRuangan">+</button>
                              </div>
                          </div>
                            <table id="dataTable" class="table table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <!-- <th scope="col"></th> -->
                                        <th scope="col">#</th>
                                        <th scope="col">Nama</th>
                                        <th scope="col">Kuota</th>
                                        
                                       
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                            </table>

                     
                            @include('components.modal-ruangan-admin')
                            


                        </div>
                    </div>

                </div>
            </div>
        </section>
    </main><!-- End #main -->
    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/handlebars@latest/dist/handlebars.js"></script>
        <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js">
        </script>
        <script type="text/javascript" charset="utf8"
            src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js">
        </script>
        <script type="text/javascript" charset="utf8"
            src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js">
        </script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js">
        </script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js">
        </script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js">
        </script> 
        <script>
           
           $(document).ready(function () {
                $('#createRuangan').click(function () {
                    $('#createModal').modal('show');
                });
            });

        

            
            $(document).ready(function() {
                // $.ajaxSetup({
                //     headers: {
                //     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                //     }
                // });
                var template = Handlebars.compile($("#details-template").html());

                var table = $('#dataTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('admin.siswa.data-ruangan') }}",
                    columns: [
                        {
                            "data": null,
                            "sortable": false,
                            render: function(data, type, row, meta) {
                                return meta.row + meta.settings._iDisplayStart + 1;
                            }
                        },
                        {
                            data: 'nama',
                            name: 'nama'
                        },
                        {
                            data: 'kuota',
                            name: 'kuota'
                        },
                       
                       
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    dom: 'Bfrtip',
                    buttons: [{
                            extend: 'excelHtml5',
                            text: '<i class="bi bi-file-earmark-excel-fill"></i>',
                            className: 'btn btn-primary btn-sm',
                            titleAttr: 'Excel',
                            init: function(api, node, config) {
                                $(node).removeClass('dt-button')
                            }
                        },
                        {
                            extend: 'pdfHtml5',
                            text: '<i class="bi bi-file-earmark-pdf-fill"></i>',
                            className: 'btn btn-secondary btn-sm',
                            titleAttr: 'PDF',
                            init: function(api, node, config) {
                                $(node).removeClass('dt-button')
                            }
                        },

                    ],
                    order: [
                        [1, 'asc']
                    ]
                });
                // Add event listener for opening and closing details
                $('#dataTable tbody').on('click', 'td.dt-control', function() {
                    var tr = $(this).closest('tr');
                    var row = table.row(tr);

                    if (row.child.isShown()) {
                        // This row is already open - close it
                        row.child.hide();
                        tr.removeClass('shown');
                    } else {
                        // Open this row
                        row.child(template(row.data())).show();
                        tr.addClass('shown');
                    }
                });
            });

            function editFunc(id) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.siswa.editRuangan') }}",
                    data: {
                        id: id,
                        _token: "{{ csrf_token() }}"
                    },
                    dataType: 'json',
                    success: function(res) {

                        $('.modal-title-nilai').html("Edit Ruangan");
                        $('#scrollingModal').modal('show', {
                            keyboard: false
                        });
                        $('#id').val(res.data.id);
                        // console.log(res.data.id)
                        $('#nama').val(res.data.nama);
                        $('#kuota').val(res.data.kuota);
                        

                    }
                });
            }
         
            function changeDateFormat(date) {
                var now = new Date(date);
                var day = ("0" + now.getDate()).slice(-2);
                var month = ("0" + (now.getMonth() + 1)).slice(-2);
                return today = now.getFullYear() + "-" + (month) + "-" + (day);
            }
            $(document).ready(function () {
        $('#simpan').on('click', function () {
            $.ajax({
                type: "POST",
                dataType: 'json',
                url: "{{ route('admin.siswa.updateRuangan') }}",
                data: {
                    id: $('#id').val(),
                    nama: $('#nama').val(),
                    kuota: $('#kuota').val(),
                    "_token": "{{ csrf_token() }}"
                },
                success: function (res) {
                    if (res.success) {
                        // Perbarui DataTable setelah berhasil
                        $('#dataTable').DataTable().ajax.reload();
                        
                        // Sembunyikan modal
                        $('#scrollingModal').modal('hide');
                        
                        // Tampilkan pesan sukses
                        Swal.fire({
                            icon: 'success',
                            title: 'Data Berhasil Diubah',
                            showConfirmButton: true,
                        });
                    } else {
                        // Tampilkan pesan kesalahan jika perlu
                        Swal.fire({
                            icon: 'error',
                            title: 'Data Gagal Diubah',
                            text: res.error, // Jika server mengirim pesan error
                            showConfirmButton: true,
                        });
                    }
                },
                error: function (xhr, status, error) {
                    // Tangani kesalahan jika ada kesalahan pada permintaan AJAX
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: 'Gagal mengirim permintaan AJAX',
                        showConfirmButton: true,
                    });
                }
            });
        });
    });
            function hapusFunc(id) {
                Swal.fire({
                    title: 'Apakah anda yakin?',
                    text: "Data akan dihapus!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Hapus!'
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            type: "POST",
                            url: "{{ route('admin.siswa.hapusRuangan') }}",
                            data: {
                                id: id,
                                _token: "{{ csrf_token() }}"
                            },
                            dataType: 'json',
                            success: function(res) {
                                if (res.success) {
                                    Swal.fire(
                                        'Terhapus!',
                                        'Data berhasil dihapus.',
                                        'success'
                                    ).then(function() {
                                        $('#dataTable').DataTable().ajax.reload()
                                    });
                                }
                            }
                        });
                    }
                })
            }
            </script>
            
        <script id="details-template" type="text/x-handlebars-template">

<div class="container">
  <div class="row">
    <div class="col-sm-4">
        <table cellpadding="5" cellspacing="0" style="padding-left:50px;">
        <b>Data Diri</b>
        <tr>
          <td>No Pendaftaran:</td>
          <td>@{{no_pendaftaran}}</td>
        </tr>
        <tr>
          <td>Jalur:</td>
          <td>@{{jalur}}</td>
        </tr>
        <tr>
        <td>Asal Sekolah:</td>
          <td>@{{asal_sekolah}}</td>
        </tr>
        <tr>
          <td>NPSN Asal Sekolah:</td>
          <td>@{{npsn_asal_sekolah}}</td>
        </tr>
        <tr>
          <td>NISN:</td>
          <td>@{{nisn}}</td>
        </tr>
        <tr>
          <td>NIK:</td>
          <td>@{{nik}}</td>
        </tr>
        <tr>
          <td>Kewernegaraan:</td>
          <td>@{{kewernegaraan}}</td>
        </tr>
        <tr>
          <td>Nama Lengkap:</td>
          <td>@{{nama_lengkap}}</td>
        </tr>
        <tr>
          <td>Tempat Lahir:</td>
          <td>@{{tempat}}</td>
        </tr>
        <tr>
          <td>Tanggal Lahir:</td>
          <td>@{{tanggal_lahir}}</td>
        </tr>
        <tr>
        <td>Jenis Kelamin:</td>
        <td>@{{jenis_kelamin}}</td>
        </tr>
        <tr>
          <td>Agama:</td>
          <td>@{{agama}}</td>
        </tr>
        <tr>
          <td>Email:</td>
          <td>@{{email}}</td>
        </tr>
        <tr>
          <td>No. Handphone:</td>
          <td>
            @{{handphone}}
          </td>
        </tr>
      </table>
    </div>
    <div class="col-sm-4">
        <table cellpadding="5" cellspacing="0" style="padding-left:50px;">
        <b>Data Orang Tua / Wali</b>
        <tr>
          <td>Status Ayah:</td>
          <td>@{{stts_ayah}}</td>
        </tr>
        <tr>
          <td>Nama Ayah:</td>
          <td>@{{nama_ayah}}</td>
        </tr>
        <tr>
        <td>Tempat dan Tanggal Lahir:</td>
          <td>@{{tempat_lahir_ayah}},@{{tgl_lahir_ayah}}</td>
        </tr>
        <tr>
          <td>Pekerjaan:</td>
          <td>@{{pekerjaan_ayah}}</td>
        </tr>
        <tr>
          <td>No. HP:</td>
          <td>@{{no_hp_ayah}}</td>
        </tr>
        <tr>
          <td>Status Ibu:</td>
          <td>@{{stts_ibu}}</td>
        </tr>
        <tr>
          <td>Nama Ibu:</td>
          <td>@{{nama_ibu}}</td>
        </tr>
        <tr>
        <td>Tempat dan Tanggal Lahir:</td>
          <td>@{{tempat_lahir_ibu}},@{{tgl_lahir_ibu}}</td>
        </tr>
        <tr>
          <td>Pekerjaan:</td>
          <td>@{{pekerjaan_ibu}}</td>
        </tr>
        <tr>
          <td>No. HP:</td>
          <td>@{{no_hp_ibu}}</td>
        </tr>
        <tr>
          <td>Nama Wali:</td>
          <td>@{{nama_wali}}</td>
        </tr>
        <tr>
          <td>Tanggal Lahir:</td>
          <td>@{{tgl_lahir_wali}}</td>
        </tr>
        <tr>
          <td>Pekerjaan:</td>
          <td>@{{pekerjan_wali}}</td>
        </tr>
        <tr>
          <td>No. HP:</td>
          <td>@{{no_hp_wali}}</td>
        </tr>
      </table>
    </div>
    
    <div class="col-sm-4">
        <table cellpadding="5" cellspacing="0" style="padding-left:50px;">
          <b>Data Alamat</b>
          <tr>
              <td>Status Tempat Tinggal: </td>
              <td>@{{stts_tempat_tinggal}}</td>
          </tr>
          <tr>
              <td>Alamat:</td>
              <td>@{{alamat}}</td>
          </tr>
          <tr>
              <td>RT/RW:</td>
              <td>@{{rt}}/@{{rw}}</td>
          </tr>
          <tr>
              <td>Desa:</td>
              <td>@{{desa}}</td>
          </tr>
          <tr>
              <td>Kecamatan:</td>
              <td>@{{kecamatan}}</td>
          </tr>
          <tr>
              <td>Kabupaten:</td>
              <td>@{{kabupaten}}</td>
          </tr>
          <tr>
              <td>Provinsi:</td>
              <td>@{{provinsi}}</td>
          </tr>
          <tr>
              <td>kode Pos:</td>
              <td>@{{kode_pos}}</td>
          </tr>
          <tr>
              <td>Koordinat:</td>
              <td>  
                <iframe 
                  width="170" 
                  height="170" 
                  frameborder="0" 
                  scrolling="no" 
                  marginheight="0" 
                  marginwidth="0" 
                  src="https://maps.google.com/maps?q=@{{koordinat_alamat}}&hl=es&z=14&amp;output=embed">
              
              </td>
          </tr>
      </table>
    </div>  
    
  </div>
</div>
 

</script>

    @endpush
@endsection
