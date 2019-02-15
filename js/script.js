jQuery(document).ready(function ($){

});
$(".edit-button").click(function(){
	$(this).parent().siblings().slideDown(200);
});
$(".form-edit-sub_cancel").click(function(){
	$(this).parent().parent().parent().slideUp(200);
});
$(".err__ok").click(function(){
  $(this).parent().addClass("hide");
  $(this).parent().removeClass("err__ok");
})