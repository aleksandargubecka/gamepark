(function ($){

    $(document).ready(function (){

        $('.delete-activity').click(function (e){
            var is_confirmed = confirm('Are you sure?');
            if(!is_confirmed){
                e.preventDefault();
            }
        });

            $('#gallery a').magnificPopup({
                gallery: {
                    enabled: true
                },
                type:'image'
            });
    })
})(jQuery);