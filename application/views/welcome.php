<?php
    defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>REST Server Tests</title>

    <style>

    ::selection { background-color: #E13300; color: white; }
    ::-moz-selection { background-color: #E13300; color: white; }

    body {
        background-color: #FFF;
        margin: 40px;
        font: 16px/20px normal Helvetica, Arial, sans-serif;
        color: #4F5155;
        word-wrap: break-word;
    }

    a {
        color: #039;
        background-color: transparent;
        font-weight: normal;
    }

    h1 {
        color: #444;
        background-color: transparent;
        border-bottom: 1px solid #D0D0D0;
        font-size: 24px;
        font-weight: normal;
        margin: 0 0 14px 0;
        padding: 14px 15px 10px 15px;
    }

    code {
        font-family: Consolas, Monaco, Courier New, Courier, monospace;
        font-size: 16px;
        background-color: #f9f9f9;
        border: 1px solid #D0D0D0;
        color: #002166;
        display: block;
        margin: 14px 0 14px 0;
        padding: 12px 10px 12px 10px;
    }

    #body {
        margin: 0 15px 0 15px;
    }

    p.footer {
        text-align: right;
        font-size: 16px;
        border-top: 1px solid #D0D0D0;
        line-height: 32px;
        padding: 0 10px 0 10px;
        margin: 20px 0 0 0;
    }

    #container {
        margin: 10px;
        border: 1px solid #D0D0D0;
        box-shadow: 0 0 8px #D0D0D0;
    }
    </style>
</head>
<body>

<div id="container">
    <h1>e-sptpd Web Service</h1>

    <div id="body">

        <h2><a href="<?php echo site_url(); ?>">Home</a></h2>

        <p>
            Berikut daftar web service yang tersedia di sptpd
        </p>

        <p>
            The master project repository is
            <a href="https://github.com/chriskacerguis/codeigniter-restserver" target="_blank">
                https://github.com/chriskacerguis/codeigniter-restserver
            </a>
        </p>

        <p>
            Click on the links to check whether the REST server is working.
        </p>

        <ol>
            <li><a href="<?php echo site_url('api/pendataan/wp'); ?>">Wajib Pajak/Kelurahan</a> - defaulting to JSON</li>
            <li><a href="<?php echo site_url('api/pendataan/wp/group/1'); ?>">Wajib Pajak/Kecamatan</a> - defaulting to JSON</li>
            <li><a href="<?php echo site_url('api/pendataan/op'); ?>">Objek Pajak/Kelurahan</a> - defaulting to JSON</li>
            <li><a href="<?php echo site_url('api/pendataan/op/group/1'); ?>">Objek Pajak/Kecamatan</a> - defaulting to JSON</li>
            <li><a href="<?php echo site_url('api/transaksi/ketetapan/awal/20160101/akhir/20160131'); ?>">Ketetapan</a> - defaulti JSON<br>
            <li><a href="<?php echo site_url('api/transaksi/ketetapan/awal/20160101/akhir/20160131/group/1'); ?>">Ketetapan Perkecamatan</a> - defaulti JSON<br>
            <li><a href="<?php echo site_url('api/transaksi/ketetapan/awal/20160101/akhir/20160131/group/2'); ?>">Ketetapan Perkelurahan</a> - defaulti JSON<br>
                Parameter Input :
                <ol>
                    <li>(awal) Periode Awal : 'YYYYMMDD'</li>
                    <li>(akhir) Periode Akhir : 'YYYYMMDD'</li>
                    
                </ol>
                Parameter Output :
                <ol>
                    <li>kode: </li>
                    <li>uraian : </li>
                    <li>pokok :</li>
                    <li>denda: </li>
                    <li>bunga: </li>
                    <li>total: </li>
                </ol>
            </li>
            Contoh Output:
           <p> [{"kode":"1","uraian":"HOTEL","pokok":"45540475314","denda":"0","bunga":"0","total":"4554047533"},
                <br>{"kode":"2","uraian":"RESTORAN","pokok":"210545042503","denda":"113263707","bunga":"113858491","total":"21055099046"},
                <br>{"kode":"3","uraian":"HIBURAN","pokok":"14320149835","denda":"0","bunga":"0","total":"1963034318"},
                <br>{"kode":"4","uraian":"REKLAME","pokok":"3447507278","denda":"9511106","bunga":"10039346","total":"871886586"},
                <br>{"kode":"6","uraian":"PARKIR","pokok":"20162461285","denda":"0","bunga":"0","total":"5040615321"},
                <br>{"kode":"7","uraian":"AIR TANAH","pokok":"2335793375","denda":"6682615","bunga":"6682615","total":"473841291"}]
           </p> 
            <li><a href="<?php echo site_url('api/transaksi/realisasi/awal/20160101/akhir/20160131'); ?>">Realisasi</a> - defaulti JSON<br>
                Parameter Input :
            <li><a href="<?php echo site_url('api/transaksi/realisasi/awal/20160101/akhir/20160131/group/1'); ?>">Realisasi Perkecamatan</a> - defaulti JSON<br>
            <li><a href="<?php echo site_url('api/transaksi/realisasi/awal/20160101/akhir/20160131/group/2'); ?>">Realisasi Perkelurahan</a> - defaulti JSON<br>
                            <ol>
                    <li>(awal) Periode Awal : 'YYYYMMDD'</li>
                    <li>(akhir) Periode Akhir : 'YYYYMMDD'</li>
                    
                </ol>
                Parameter Output :
                <ol>
                    <li>kode: </li>
                    <li>uraian : </li>
                    <li>pokok :</li>
                    <li>denda: </li>
                    <li>bunga: </li>
                    <li>total: </li>
                </ol>
            </li>
        </ol>
            <p>Catatan:
            <ul>
            <li>Untuk Mendapatkan format xml  silahkan tambahkan /format/xml di ujung url</li>
            <li>Untuk Mendapatkan format csv  silahkan tambahkan /format/csv di ujung url</li>
            <li>Untuk Mendapatkan format html  silahkan tambahkan /format/html di ujung url</li>
            </ul>
    </div>

    <p class="footer">Page rendered in <strong>{elapsed_time}</strong> seconds. <?php echo  (ENVIRONMENT === 'development') ?  'CodeIgniter Version <strong>' . CI_VERSION . '</strong>' : '' ?></p>
</div>

<script src="https://code.jquery.com/jquery-1.11.3.js"></script>

<script>
    // Create an 'App' namespace
    var App = App || {};

    // Basic rest module using an IIFE as a way of enclosing private variables
    App.rest = (function (window, $) {
        // Fields
        var _alert = window.alert;
        var _JSON = window.JSON;

        // Cache the jQuery selector
        var _$ajax = null;

        // Methods (private)

        /**
         * Called on Ajax done
         *
         * @return {undefined}
         */
        function _ajaxDone(data) {
            // The 'data' parameter is an array of objects that can be iterated over
            _alert(_JSON.stringify(data, null, 2));
        }

        /**
         * Called on Ajax fail
         *
         * @return {undefined}
         */
        function _ajaxFail() {
            _alert('Oh no! A problem with the Ajax request!');
        }

        /**
         * On Ajax request
         *
         * @param {HTMLElement} $this Current element selected
         * @return {undefined}
         */
        function _ajaxEvent($this) {
            $.ajax({
                    // URL from the link that was 'clicked' on
                    url: $this.attr('href')
                })
                .done(_ajaxDone)
                .fail(_ajaxFail);
        }

        /**
         * Bind events
         *
         * @return {undefined}
         */
        function _bindEvents() {
            // Namespace the 'click' event
            _$ajax.on('click.app.rest.module', function (event) {
                event.preventDefault();

                // Pass this to the Ajax event function
                _ajaxEvent($(this));
            });
        }

        /**
         * Cache the DOM node(s)
         *
         * @return {undefined}
         */
        function _cacheDom() {
            _$ajax = $('#ajax');
        }

        // Public API
        return {
            init: function () {
                // Cache the DOM and bind event(s)
                _cacheDom();
                _bindEvents();
            }
        };
    }(window, window.jQuery));

    // DOM ready event
    $(function () {
        // Initialise the App module
        App.rest.init();
    });

</script>

</body>
</html>
