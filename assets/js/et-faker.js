(function($) {
	$(document).ready (function () {
    var max_fields      = 15;
    var wrapper         = $(".input_fields_wrap");
    var add_button      = $(".add_field_button");

    var x = 0;
    $(add_button).click(function(e){
      e.preventDefault();
      if(x < max_fields){
        x++;
        $(wrapper).append('<div class="row cf-row"><div class="col-md-8"><input class="form-control custom-field" type="text" placeholder="custom_field_name|custom_field_value"></div><div class="col-md-4 remove-cf"><a href="#" class="remove_field"><i class="fa fa-times fa-lg" aria-hidden="true"></i></a><a href="#" id="et-info"><i class="fa fa-info-circle" aria-hidden="true"></i></a></div></div>');
        $('#et-info').click(function(event) {
          $('#log').hide();
          $('#et-help').fadeIn();
        });
      }
    });
    $(wrapper).on("click",".remove_field", function(e){
      e.preventDefault(); $(this).parent('div').parent('div').remove(); x--;
    })
    var ajaxurl = $("#ajaxurl").val();
    //Ajax Add Post
		$('#add-post').click(function(){
      $('#et-help').hide();
      $('#et-info-hide').hide();
			var post_type = $('#post_type').val(),
				  count_post = $('#count_post').val(),
          post_author = $('#post_author').val(),
          post_status = $('#post_status').val();
      var custom_field = new Array();
      $('.custom-field').each(function (index, value) {
        custom_field.push($(this).attr('value'));
      });
			var data = {
            'action': 'et_faker_add_post',
            'post_type': post_type,
            'custom_field' : custom_field,
            'post_author'    : post_author,
            'post_status' : post_status,
      };
      for (var i = 0; i < count_post; i++) {
        jQuery.ajax({
          type: "POST",
          url: ajaxurl,
          data: data,
          action: 'et_faker_add_post',
          beforeSend : function(xhr, opts){
            $('#log').html("<p style='color:red;'>Start...Please wait few minutes...</p>");
            $('#log').css("display","block");
          },
          success: function(res) {
            if(res.success){
              var result = '';
              $.each(res.data, function( key, value ) {
                result += '<a target="_blank" href="' +value['url']+ '">' +value['title']+ '</a><br>';
              });
              $('#log').append(result);
            }else{
              $('#log').append('False');
            }
          }
        });
      }
		});
    $('#et-info').click(function(event) {
      $('#et-help').fadeIn();
      $('#et-info-hide').show();
    });
    $('#et-info-hide').click(function(event) {
      $('#et-help').fadeOut('slow');
    });
    //Ajax Add User
    $('#add-user').click(function(event) {
      var count_user = $('#count_user').val(),
          user_role = $('#user_role').val();
      var data = {
            'action': 'et_faker_add_user',
            'count_user': count_user,
            'user_role' : user_role,
      };
      for (var i = 0; i < count_user; i++) {
        jQuery.ajax({
          type: "POST",
          url: ajaxurl,
          data: data,
          action: 'et_faker_add_user',
          beforeSend : function(xhr, opts){
            $('#log').html("<p style='color:red;'>Start...Please wait few minutes...</p>");
            $('#log').css("display","block");
          },
          success: function(res) {
            console.log(res);
            if(res.success){
              var result = '';
              $.each(res.data, function( key, value ) {
                result += '<a target="_blank" href="' +value['url']+ '">' +value['user_login']+ '</a><br>';
              });
              $('#log').append(result);
            }else{
              $('#log').append('False');
            }
          }
        });
      }
    });
	});
}(jQuery));