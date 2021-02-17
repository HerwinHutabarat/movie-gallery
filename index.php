<!DOCTYPE html>
<html lang="en">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php
    $basePath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $domain = "https://5f50ca542b5a260016e8bfb0.mockapi.io/api/v1/movies";
?>
<head>
    <link rel="stylesheet" type="text/css" href="<?php echo $basePath.'assets/bootstrap/css/bootstrap.min.css' ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo $basePath.'assets/popover/popover.css' ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo $basePath.'assets/fontawesome/css/all.min.css' ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo $basePath.'assets/jquery-ui/css/jquery.ui.css' ?>">
    <style media="screen">
      body{
      	background-image: url("<?php echo $basePath.'img/bg-tech-white.jpg' ?>");
      	-webkit-background-size: cover;
      	-moz-background-size: cover;
      	-o-background-size: cover;
      	background-attachment: fixed;
      	background-size: cover;
      	background-repeat: no-repeat;
      }
      div.gallery {
        box-shadow: 0 0 15px 5px #343A40;
      }

      div.gallery:hover {
        border: 1px solid #777;
      }

      div.gallery img {
        width: 100%;
        height: auto;
        text-align: center;
      }

      div.gallery, div.gallery:hover, div.gallery img {
        border-radius: 10px;
      }

      img.lazy {
        display: block;
      }

      .gallery-dt {
        cursor: pointer;
        text-decoration: underline;
      }

    </style>
</head>
<body>
    <div class="row">
        <div class="col-md-12">
            <div class="jumbotron p-1 bg-dark text-white">
                <div class="row text-center">
                    <div class="col-md-12">
                      <i class="fas fa-images fa-4x"></i>
                      <h2 style="text-shadow: 2px 2px #1281AF;">MOVIE GALLERY</h2>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2"></div>
                    <div class="col-md-4">
                      <label for="search">Search</label>
                      <input type="text" class="form-control form-control-sm" placeholder="search album..." name="search" id="search" />
                    </div>
                    <div class="col-md-4 text-right">
                      <label for="search">Filter By Date</label>
                      <select class="form-control form-control-sm" name="filter" id="filter">
                          <option value="">-- Select Date --</option>
                      </select>
                    </div>
                    <div class="col-md-2"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row pl-5 pr-5">
        <div class="col-md-12 pl-5 pr-5">
            <div class="row" id="parent">
                <?php

                    if (@fopen($domain, "r") == false) {
                      die('<center><h5>Unable to get data form API</h5></center>');
                    }
                    $gallery = json_decode(file_get_contents($domain));

                    if(!empty($gallery)) {
                    $no = 1;
                    foreach($gallery as $data)  { ?>

                        <div id="gallery<?=$data->id?>" class="col-md-4 col-sm-12 gallery popover-wrapper" data-value="<?=$data->id?>">
                            <a class="thumbs" target="_blank" href="<?=$data->image?>">
                                <img class="lazy popover-title" src="<?=$data->image?>" alt="<?=$data->title?>">
                            </a>
                            <div class="popover-content">
                              <p class="popover-message"><?=$data->title?></p>
                              <div class="row">
                                <div class="col-md-4 text-right"><i class="far fa-clock"></i> Showtime</div>
                                <div class="col-md-8"><?=$data->showTime?></div>
                              </div>
                              <div class="row">
                                <div class="col-md-4 text-right"><i class="fas fa-link"></i> Source</div>
                                <div class="col-md-8"><?=$data->image?></div>
                              </div>
                              <div class="row">
                                <div class="col-md-4 text-right"><i class="far fa-thumbs-up"></i> Like</div>
                                <div class="col-md-8"><?=$data->like?></div>
                              </div>
                              <div class="row">
                                <div class="col-md-12 text-right">
                                  <small class="gallery-dt" data-value="<?=$data->id?>">Further Details</small>
                                </div>
                              </div>
                            </div>
                        </div>

                    <?php $no++; }

                    }
                ?>

            </div>
        </div>
    </div>
    <div class="modal fade" id="modalDetail" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
      			  <div class="modal-header popup-header">
      				  <h5 class="modal-title" id="exampleModalLabel">Gallery Detail</h5>
      			  </div>
              <div class="modal-body">
              </div>
              <div class="modal-footer">
                <button class="btn btn-sm btn-block btn-secondary modal-close" data-dismiss="modal">CLOSE</button>
              </div>
            </div>
        </div>
    </div>
</body>
</html>
<script src="<?php echo $basePath.'assets/jquery/jquery.min.js' ?>"></script>
<script src="<?php echo $basePath.'assets/jquery-ui/js/jquery.ui.js' ?>"></script>
<script src="<?php echo $basePath.'assets/bootstrap/js/bootstrap.min.js' ?>"></script>
<script src="<?php echo $basePath.'node_modules/jquery-lazy/jquery.lazy.min.js' ?>"></script>
<script>

    var gallery = <?=json_encode($gallery)?>;
    let filteredByDate = gallery.reduce(function (r, a) {
            r[a.showTime.substring(0, 7)] = r[a.showTime.substring(0, 7)] || [];
            r[a.showTime.substring(0, 7)].push(a);
            return r;
        }, Object.create(null));

    $(function() {
        var autocomplete = [];
        $.each(gallery, (key, data) => {
          autocomplete.push(data.title);
        });
        $("#search").autocomplete({
          source: autocomplete,
          select: function (e, ui) {
            console.log(ui['item'].value);

            var result = gallery.filter(function(val) {
              return val.title.toUpperCase().match(ui['item'].value.toUpperCase());
            });
            refreshParent(result);
          }
        });
    });

    $(function() {
      $('.lazy').lazy();
      appendOptFilter(filteredByDate);
    });

    $(".gallery-dt").on("click", function() {
      var id = $(this).data('value');
      getGalleryById(id);
    });

    $("#search").on("keyup", function() {
      $("#filter").val("");
      var value = $(this).val();
      var result = gallery.filter(function(val) {
        return val.title.toUpperCase().match(value.toUpperCase());
      });
      refreshParent(result);
    });

    function refreshParent(gallery) {
      var replaceParent = '';
      $.each(gallery, (key, data) => {
        replaceParent += `<div id="gallery${data.id}" class="col-md-4 col-sm-12 gallery popover-wrapper" data-value="${data.id}">
            <a class="thumbs" target="_blank" href="${data.image}">
                <img class="lazy popover-title" src="${data.image}" alt="${data.title}">
            </a>
            <div class="popover-content">
              <p class="popover-message">${data.title}</p>
              <div class="row">
                <div class="col-md-4 text-right">Showtime</div>
                <div class="col-md-8"><i class="far fa-clock"></i> ${data.showTime}</div>
              </div>
              <div class="row">
                <div class="col-md-4 text-right"><i class="fas fa-link"></i> Source</div>
                <div class="col-md-8">${data.image}</div>
              </div>
              <div class="row">
                <div class="col-md-4 text-right">Like</div>
                <div class="col-md-7"><i class="far fa-thumbs-up"></i> ${data.like}</div>
              </div>
              <div class="row">
                <div class="col-md-12 text-right">
                  <small class="gallery-dt" data-value="${data.id}">Further Details</small>
                </div>
              </div>
            </div>
        </div>`
      });
      $("#parent").html(replaceParent);
      $(".gallery-dt").on("click", function() {
        var id = $(this).data('value');
        getGalleryById(id);
      });
    }

    function appendOptFilter(filteredByDate) {
      var optFilterByDate = '';
      $.each(filteredByDate, (key, val) => {
        optFilterByDate += `<option value="${key}">${key}</option>`;
      });
      $("#filter").append(optFilterByDate);
    }

    $("#filter").on("change", function() {
      $("#search").val("");
      refreshParent(filteredByDate[$(this).val()]);
    });

    function getGalleryById(id) {
      $.ajax({
        type: 'GET',
        url: `https://5f50ca542b5a260016e8bfb0.mockapi.io/api/v1/movies/${id}`,
        success: (response) => {
          $("#modalDetail .modal-body").append(`<div class="row">`);
          $("#modalDetail .modal-body").append(`<div class="col-md-12"><img src="${response.image}" alt="${response.title}" width="100%" height="380"></div>`);
          $("#modalDetail .modal-body").append(`</div>`);
          $("#modalDetail .modal-body").append(`<div class="row">`);
          $("#modalDetail .modal-body").append(`<div class="col-md-12 text-center"><strong>${response.title}</strong></div>`);
          $("#modalDetail .modal-body").append(`</div>`);
          $("#modalDetail .modal-body").append(`<div class="row">`);
          $("#modalDetail .modal-body").append(`<div class="col-md-12"><strong><i class="far fa-clock"></i> Showtime</strong></div>`);
          $("#modalDetail .modal-body").append(`<div class="col-md-12">${response.showTime}</div>`);
          $("#modalDetail .modal-body").append(`</div>`);
          $("#modalDetail .modal-body").append(`<div class="row">`);
          $("#modalDetail .modal-body").append(`<div class="col-md-12"><strong><i class="fas fa-link"></i> Source</strong></div>`);
          $("#modalDetail .modal-body").append(`<div class="col-md-12">${response.image}</div>`);
          $("#modalDetail .modal-body").append(`</div>`);
          $("#modalDetail .modal-body").append(`<div class="row">`);
          $("#modalDetail .modal-body").append(`<div class="col-md-12"><strong><i class="far fa-thumbs-up"></i> Like</strong></div>`);
          $("#modalDetail .modal-body").append(`<div class="col-md-12">${response.like}</div>`);
        },
        error: (err) => {
          alert('Terjadi Kesalahan');
        }
      });
      $("#modalDetail").modal("show");
      $(".modal-close").on("click", () => {
        $("#modalDetail .modal-body").html(``);
      });
    }


</script>
