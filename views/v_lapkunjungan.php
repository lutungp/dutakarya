<script src="https://code.highcharts.com/highcharts.js"></script>
<link type="text/css" rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css" />
<script src="<?php echo BASE_URL ?>/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<section class="content">
    <div class="row">
        <div class="col-12">
            <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-4 col-sm-6">
                        <div class="form-group">
                            <label>Tanggal:</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                </div>
                                <input type="text" id="datefilter" class="form-control float-right">
                                &nbsp;<button type="button" id="btn-filter" class="btn btn-primary"><i class="fas fa-search"></i>&nbsp;Cari</button>
                                <!-- &nbsp;<button type="button" id="btn-chart" class="btn btn-default"><i class="fas fa-chart-bar"></i>&nbsp;Lihat Grafik</button> -->
                            </div>
                        </div>
                    </div>
                    <!-- /.input group -->
                </div>
            </div>
            <div class="container-chart">
                <div id="container" style="height: 400px"></div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <table id="listperlayanan" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <!-- <th>No.</th> -->
                            <th>Layanan</th>
                            <th>Baru</th>
                            <th>Lama</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                </table>
                <input type="hidden" id="instalasi"/>
                <input type="hidden" id="layanan">
                <input type="hidden" id="groupjaminan">
                <input type="hidden" id="tipejaminan">
            </div>
            <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    <!-- /.col -->
    </div>
      <!-- /.row -->
</section>
<script>
    var oTable = "";
    $('#datefilter').daterangepicker({
        locale: {
            format: 'DD/MM/YYYY'
        }
    });
    var datefilter = $('#datefilter').val();
    datefilter = datefilter.split(" - ");
    $(function () {
        oTable = $("#listperlayanan").DataTable({
            "responsive": true,
            "autoWidth": false,
            "bProcessing": true,
            "bServerSide": true,
            "paging":   false,
            "ordering": false,
            "info":     false,
            "sAjaxSource": "<?php echo BASE_URL ?>/controllers/C_lapkunjunganpasien.php?action=getPerLayanan",
            "fnServerParams": function ( aoData ) {
                aoData.push( 
                    { 
                        "name": "tglawal", 
                        "value": moment(datefilter[0], 'DD/MM/YYYY').format("YYYY-MM-DD")
                    },
                    { 
                        "name": "tglakhir", 
                        "value": moment(datefilter[1], 'DD/MM/YYYY').format("YYYY-MM-DD")
                    },
                    {
                        "name": "instalasi", 
                        "value": $('#instalasi').val()
                    },
                    {
                        "name": "layanan", 
                        "value": $('#layanan').val()
                    },
                    {
                        "name": "groupjaminan", 
                        "value": $('#groupjaminan').val()
                    },
                    {
                        "name": "tipejaminan", 
                        "value": $('#tipejaminan').val()
                    }
                );
            }
        });
        $("#btn-filter").on("click", function () {
            oTable.ajax.reload();
            var datefilter = $('#datefilter').val();
            datefilter = datefilter.split(" - ");
            getData(datefilter);
        });
        
    });

    var options = {
        chart: {
            type: 'column',
            events: {
                load: getData(datefilter)
            }
        },
        title: {
            text: ''
        },
        xAxis: {
            categories: JSON.parse('<?php echo $dataparse["layanan"]?>')
        },
        yAxis: {
            allowDecimals: false,
            min: 0,
            title: {
                text: 'Number of fruits'
            }
        },
        tooltip: {
            formatter: function () {
                return '<b>' + this.x + '</b><br/>' +
                this.series.name + ': ' + this.y + '<br/>' +
                'Total: ' + this.point.stackTotal;
            }
        },
        plotOptions: {
            column: {
                stacking: 'normal'
            }
        },
        series: []
    }

    var chart = Highcharts.chart('container', options);

    function getData(datefilter) {
        $.ajax({
            url: "<?php echo BASE_URL ?>/controllers/C_lapkunjunganpasien.php?action=getchartdata",
            type: "post",
            data: {
                tglawal : moment(datefilter[0], 'DD/MM/YYYY').format("YYYY-MM-DD"),
                tglakhir : moment(datefilter[1], 'DD/MM/YYYY').format("YYYY-MM-DD"),
                instalasi : $('#instalasi').val(),
                layanan : $('#layanan').val(),
                groupjaminan : $('#groupjaminan').val(),
                tipejaminan : $('#tipejaminan').val()
            },
            success : function (res) {
                var result = JSON.parse(res);
                var dataLayanan = [];
                var layanan = JSON.parse('<?php echo $dataparse["layanan"]?>');
                var pasienbaru = [];
                var pasienlama = [];
                for (let index = 0; index < layanan.length; index++) {
                    const element = layanan[index];
                    hh = result.filter(p=>p[0]==element);
                    if(hh[0] !== undefined) {
                        pasienbaru.push(hh[0][1]);
                        pasienlama.push(hh[0][2]);
                    } else {
                        pasienbaru.push(0);
                        pasienlama.push(0);
                    }
                }
                
                options.series = []

                options.series.push({
                    name : 'Pasien Baru',
                    data : pasienbaru,
                    stack : 'baru'
                });

                options.series.push({
                    name : 'Pasien Lama',
                    data : pasienlama,
                    stack : 'baru'
                });

                Highcharts.chart('container', options);
            }
        });
    }
    
    function getLayanan() {
        
    }

</script>