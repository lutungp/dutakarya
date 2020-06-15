<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
            <!-- The time line -->
                <div class="timeline"></div>
            </div>
        </div>
    </div>
</section>
<script>
    $(function(){
        $.get("<?php echo BASE_URL ?>/controllers/C_dashboard.php?action=timeline", function(data, status){
            var res = JSON.parse(data);
            var html = "";

            let group = res.reduce((r, a) => {
                r[a.tanggal] = [...r[a.tanggal] || [], a];
                return r;
            }, {});

            const groups = Object.values(group);
            groups.forEach(function(elem){
                var tanggal = moment(elem[0].tanggal, "YYYY-MM-DD").format("LL");
                html += '<div class="time-label"><span class="bg-red">'+tanggal+'</span></div>';
                elem.forEach(function(val){
                    html += '<div>';
                    html += '<i class="fas fa-envelope bg-blue"></i>';
                    html += '<div class="timeline-item">';
                    html += '<span class="time"><i class="fas fa-clock"></i> 12:05</span>';
                    var star = '';
                    var readmore = val.readmore.split("-");
                    if(readmore[2]<1){
                        star = '<i class="fas fa-star text-warning"></i>';
                    }
                    html += '<h3 class="timeline-header">Pemberitahuan <a href="#">'+val.title+'</a>&nbsp;'+star+'</h3>';
                    html += '<div class="timeline-body">';
                    html += val.description;
                    html += '</div>';
                    html += '<div class="timeline-footer">';
                    html += "<a class='btn btn-primary btn-sm' onclick=readmore('"+val.readmore+"')>Read more</a>";
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                });
            });
            if(res.length > 0) {
                html += '<div><i class="fas fa-clock bg-gray"></i></div>';
            }
            
            $(".timeline").html(html);
        });
    });

    function readmore(param) {
        if(param.match(/A-/g)) {
            param = param.split("-");
            var gaji_id = param[1];
            window.open('<?php echo BASE_URL;?>/controllers/C_gajikaryawan.php?action=exportpdf&id=' + gaji_id);
            $.get("<?php echo BASE_URL ?>/controllers/C_gajikaryawan.php?action=view&id="+gaji_id, function(data, status){
                console.log(status);
            });
        }
    }
</script>