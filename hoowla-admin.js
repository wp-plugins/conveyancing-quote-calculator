jQuery(document).ready(function($) {
    
    var picker = $.farbtastic('#colorpicker');
    picker.setColor( $('input.color-picker').val() );

    picker.linkTo( function(color) {
        $('input.color-picker').val(color);
    });
    
});