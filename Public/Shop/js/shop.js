function alertMsg(txt){
	var ele = $("#alertMsg-mobile");
	if(ele.length == 0){
		
		$alert = $('<div class="am-modal am-modal-loading am-modal-no-btn" tabindex="-1" id="alertMsg-mobile"><div class="am-modal-dialog"><div class="am-modal-hd">系统提示</div><div class="am-modal-bd"></div></div></div>');
		$("body").append($alert);
		ele = $("#alertMsg-mobile");
	}
	if(txt){
		$(".am-modal-bd",ele).text(txt);
	}
	
	ele.modal("open");
	
	setTimeout(function(){
		ele.modal("close");
	},2500);
	
}

function loadingMsg(txt){
	var ele = $("#loading-mobile");
	if(ele.length == 0){
		
		$alert = $('<div class="am-modal am-modal-loading am-modal-no-btn" tabindex="-1" id="loading-mobile"><div class="am-modal-dialog"><div class="am-modal-hd">正在请求...</div><div class="am-modal-bd"><span class="am-icon-spinner am-icon-spin"></span>    </div>  </div></div>');
		$("body").append($alert);
		ele = $("#loading-mobile");
	}
	
	if(txt){
		$(".am-modal-hd",ele).text(txt);
	}
	
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
		$confirm = $('<div class="am-modal am-modal-confirm" tabindex="-1" id="confirm-mb"><div class="am-modal-dialog"><div class="am-modal-hd">系统消息</div><div class="am-modal-bd">你，确定要进行此操作吗？</div><div class="am-modal-footer"><span class="am-modal-btn" data-am-modal-cancel>取消</span><span class="am-modal-btn" data-am-modal-confirm>确定</span></div></div></div>');
		$("body").append($confirm);
		ele = $("#confirm-mb");
	}
	
	if(data.content){
		$(".am-modal-bd",ele).text(data.content);
	}else{
		ele.modal({
	        relatedTarget: window,
	        onConfirm: function(options) {
	         	data.action && data.action.call();
	        },
	        onCancel: function() {
	        		
	        }
      });
	}
	
    ele.modal("open");
	
	setTimeout(function(){
		//$(".am-modal-hd",ele).modal("close");
	},2500);
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
						console.log(1);
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
			$(that).button("loading");
//			var ele = loadingMsg("请求中...")；
			$.post(target, query).always(function() {
				setTimeout(function() {
					$(that).button("reset");
				}, 1400);
			}).done(function(data) {
				if (data.status == 1) {
					if (data.url) {
						alertMsg(data.info + ' 页面即将自动跳转~');
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
			});
		}
		
}) //end $.ready


window.wxshop = (function(){
	
	/**
	 * 将商品添加到购物车中
	 * @param {Object} p_id 商品ID
	 */
	function addToShoppingCart(that,p_id,target){
		var sku_id = "";
		//存在SKU标识
		if($("#hebidu_skuchecked").length == 1){
			sku_id  = $("#hebidu_skuchecked").val();
		}
		
		var query = {p_id:p_id,sku_id :sku_id}; 
		
		$.post(target, query).always(function() {
			
		}).done(function(data) {
			if (data.status == 1) {
				alertMsg("成功添加到购物车!");
			} else {
				alertMsg(data.info);				
			}
		});
		
	}
	
	
	
	return {
		addToShoppingCart:addToShoppingCart,
	}
	
	
})(window);







