$( document ).ready(function() {
    console.log( "ready!" );


    $('#ocrImage').on('change', function(e) {

        Tesseract.recognize(e.target.files[0]).then(function(result) {
            //console.log('Done: ' + result.text);
            $("#cardID").val(result.text);
        }).progress(function(result) {
            console.log(result['status'] + ' - (' + result['progress'] + ')');
        }).catch(function(result) {
            console.log(result);
        });

    });
});