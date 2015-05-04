function alertMsg(txt){
	var ele = $("#alertMsg-mobile");
	if(ele.length == 0){
		
		$alert = $('<div style="z-index:100000000;" class="am-modal am-modal-loading am-modal-no-btn" tabindex="-1" id="alertMsg-mobile"><div class="am-modal-dialog"><div class="am-modal-bd"></div></div></div>');
		$("body").append($alert);
		ele = $("#alertMsg-mobile");
	}
	if(txt){
		$(".am-modal-bd",ele).html(txt);
	}else{
		return ;
//		$(".am-modal-bd",ele).text(txt);
	}
	
	ele.modal("open");
	
	setTimeout(function(){
		ele.modal("close");
	},2500);
	
}

function loadingMsg(txt){
	var ele = $("#loading-mobile");
	if(ele.length == 0){
		
		$alert = $('<div style="z-index:100000000;" class="am-modal am-modal-loading am-modal-no-btn" tabindex="-1" id="loading-mobile"><div class="am-modal-dialog"><div class="am-modal-bd"><span class="am-icon-spinner am-icon-spin"></span></div></div></div>');
		$("body").append($alert);
		ele = $("#loading-mobile");
	}
	
//	if(txt){
//		$(".am-modal-hd",ele).text(txt);
//	}else{
//		return ;
//		$(".am-modal-bd",ele).text(txt);
//	}
	
	ele.modal("open");
	return ele;
//	setTimeout(function(){
//	},2500);
	
}

/**
 * 
 * @param {Object} data {content:"文字",action:"回调函数"}
 */
function confirmMsg(data){
	var ele = $("#confirm-mb");
	if(ele.length == 0){		
		$confirm = $('<div style="z-index:100000000;" class="am-modal am-modal-confirm" tabindex="-1" id="confirm-mb"><div class="am-modal-dialog"><div class="am-modal-bd">你，确定要进行此操作吗？</div><div class="am-modal-footer"><span class="am-modal-btn" data-am-modal-cancel>取消</span><span class="am-modal-btn" data-am-modal-confirm>确定</span></div></div></div>');
		$("body").append($confirm);
		ele = $("#confirm-mb");
		ele.on('closed.modal.amui', function() {
			$(this).removeData('amui.modal');
		});
		
	}
	
	var $confirmBtn = ele.find('[data-am-modal-confirm]');
	var $cancelBtn = ele.find('[data-am-modal-cancel]');
	$confirmBtn.off('click.confirm.modal.amui').unbind('click');
	$confirmBtn.off('click.confirm.modal.amui').bind('click', function() {
			// do something
    		data.action && data.action.apply(this,[data.relatedTarget]);
	});
	if(data.content){
		$(".am-modal-bd",ele).text(data.content);
	}
//	console.log(data);
	
    ele.modal("open");
	
//	setTimeout(function(){
		//$(".am-modal-hd",ele).modal("close");
//	},2500);
}


$(window).load(function() {
	$("body").addClass("domloaded")
//	setTimeout(function(){},1300);
	$.AMUI.progress.done();
});

$(function() {
		$.AMUI.progress.start();//.start();
		//nprogress
		$(document).ajaxStart(function() {
			$.AMUI.progress.start();
		}).ajaxStop(function() {
			$.AMUI.progress.done();
		}).ajaxComplete(function() {	
			$.AMUI.progress.inc();
		});
		
		
		$('.ajax-get').click(function() {
//			console.log("ajax-get");
			var target, query, form;
			var that = this;
			var need_confirm = false;
			query = {};
			if ($(that).attr("href") !== undefined || $(that).attr('data-href') !== undefined) {
				target = $(that).attr('href') || $(that).attr("data-href");
			}
			
//			target = $(that).attr("href");
//			console.log(target,that);
			if ($(that).hasClass('confirm')) {
				confirmMsg({
					relatedTarget:that,
					content: '确认要执行该操作吗',
					action: function() {
						console.log(arguments);
						var target = $(that).attr('href') || $(that).attr("data-href");
						ajaxpost(this, target, {});
					}
				});
				
			} else {
				ajaxpost(that, target, query);
			}
			return false;
		}); //END ajax-get
		
		//依赖jquery，scojs,
		//ajax post submit请求
		$('.ajax-post').click(function() {
			console.log("ajax-post");
			var target, query, form;
			var target_form = $(this).attr('target-form');
			var that = this;
			var need_confirm = false;
			if (($(this).attr('type') == 'submit') || (target = $(this).attr('href')) || (target = $(this).attr('url'))) {
				form = $('.' + target_form);
				
				if ($.validator && (form.hasClass("validate-form") || form.hasClass("validateForm"))) {
					if (!form.valid()) {
						alertMsg('表单验证不通过！');
						return false;
					}
				}
				
				if ($(this).attr('hide-data') === 'true') {
					//以隐藏数据作为参数
					form = $('.hide-data');
					query = form.serialize();
				} else if (form.get(0) == undefined) {
					return false;
				} else if (form.get(0).nodeName == 'FORM') {
					if ($(this).attr('url') !== undefined || $(this).attr("href") !== undefined) {
						target = $(this).attr('url') || $(this).attr("href");
					} else {
						target = form.get(0).action;
					}
					query = form.serialize();


				} else {

					query = form.find('input,select,textarea').serialize();

				}


			}
			
			if ($(this).hasClass('confirm')) {
				console.log("confirm");
				confirmMsg({
					content: '确认要执行该操作吗',
					action: function() {
						ajaxpost(that, target, query);
					}
				});
				
			} else {
				ajaxpost(that, target, query);
			}
			return false;
		}); //END ajax-post

		function ajaxpost(that, target, query) {
//			$(that).button("loading");

			var ele = loadingMsg("请求中...");
			$.post(target, query).always(function() {
				ele.modal("close");
				setTimeout(function() {
//					$(that).button("reset");					
				}, 1400);
			}).done(function(data) {
				if (data.status == 1) {
					if (data.url) {
						alertMsg(data.info + ' <br/>页面即将自动跳转~');
					} else {
						alertMsg(data.info);
					}
					
					setTimeout(function() {
						if (data.url) {
							location.href = data.url;
						} else if ($(that).hasClass('no-refresh')) {
							//不刷新
						} else {
							location.reload();
						}
					}, 1500);
				} else {

					alertMsg(data.info);
					setTimeout(function() {
						if (data.url) {
							location.href = data.url;
						} else {}
					}, 1500);
				}
			}).fail(function(){
				alertMsg("操作失败！");
			});
		}
		
}) //end $.ready


