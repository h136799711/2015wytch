window.console = window.console || (function(){  
    var c = {}; c.log = c.warn = c.debug = c.info = c.error = c.time = c.dir = c.profile  
    = c.clear = c.exception = c.trace = c.assert = function(){};  
    return c;  
})();  
(function($, w) {

	function redirectTo(url) {
		window.location.href = url;
	}

	function alertTODO(msg) {
			msg = msg || "此功能未实现";

			$.scojs_message(msg, $.scojs_message.TYPE_OK);

		}
		//进入全屏

	function requestFullScreen() {
			var de = document.documentElement;
			if (de.requestFullscreen) {
				de.requestFullscreen();
			} else if (de.mozRequestFullScreen) {
				de.mozRequestFullScreen();
			} else if (de.webkitRequestFullScreen) {
				de.webkitRequestFullScreen();
			}
		}
		//退出全屏

	function exitFullscreen() {
		var de = document;
		if (de.exitFullscreen) {
			de.exitFullscreen();
		} else if (de.mozCancelFullScreen) {
			de.mozCancelFullScreen();
		} else if (de.webkitCancelFullScreen) {
			de.webkitCancelFullScreen();
		}
	}
	
	function selectall(that,sel){
		if($(that).prop('checked')){
			$(sel).prop('checked',true);
		}else{
			$(sel).prop('checked',false);			
		}
	}

	w.myUtils = {
		redirectTo: redirectTo,
		alertTODO: alertTODO,
		exitFullscreen: exitFullscreen,
		requestFullscreen: requestFullScreen,
		selectall:selectall,
		ajaxpost:function ajaxpost(that, target, query) {
				$(that).button("loading");
				$.post(target, query).always(function() {
					setTimeout(function(){
							$(that).button("reset");
						},1400);
				}).done(function(data) {
					if (data.status == 1) {
						if (data.url) {
							$.scojs_message(data.info + ' 页面即将自动跳转~', $.scojs_message.TYPE_OK);
						} else {
							$.scojs_message(data.info, $.scojs_message.TYPE_OK);
						}
						setTimeout(function() {
							if (data.url) {
								location.href = data.url;
							} else if ($(that).hasClass('no-refresh')) {} else {
								location.reload();
							}
						}, 1500);
					} else {

						$.scojs_message(data.info, $.scojs_message.TYPE_OK);
						setTimeout(function() {
							if (data.url) {
								location.href = data.url;
							} else {}
						}, 1500);
					}
				});
			}
	};


	$(window).load(function() {
		NProgress.done();
	})
	
	
	$(function() {
			NProgress.start();
			//nprogress
			$(document).ajaxStart(function() {
				NProgress.start();
			}).ajaxStop(function() {
				NProgress.done();
			}).ajaxComplete(function() {
				NProgress.inc();
			});
			//正常confirm
			$(".normal-get").click(function(ev){
				$item = $(ev.target);
				if ($item.hasClass('confirm')) {

					var conf = $.scojs_confirm({
						content: '确认要执行该操作吗?',
						action: function() {
							$(".normal-get").removeClass("confirm").click();
						}
					});
					conf.show();
					ev.preventDefault();
					return false;
				}
				
			});
			
			$(".dropdown-toggle.avatar").hover(function() {
				$(".dropdown-toggle.avatar").dropdown("toggle");
			}).next(".dropdown-menu").hover(function() {
				$(".dropdown-toggle.avatar").dropdown("toggle");
			});

			var $fullText = $('.admin-fullText');
			$('#admin-fullscreen').on('click', function() {
				if ($fullText.text() == '全屏') {
					w.myUtils.requestFullscreen();
					$fullText.text('ESC');
				} else {
					w.myUtils.exitFullscreen();
					$fullText.text('全屏')
				}
			});

			$(window).resize(function() {
				console.log("=window resize=");
				var width = $(".admin-main").outerWidth() - $('.admin-sidebar').outerWidth() - 30;
				if (width > 0) {
					$(".admin-main-content").outerWidth(width);
				}
			});
			
			//一般是select 框
			$(".sle_ajax_post").change(function(ev){
				var item = $(ev.target);
				var query = item.serialize();
				if (item.hasClass('confirm')) {

					var conf = $.scojs_confirm({
						content: '确认要执行该操作吗?',
						action: function() {
							sleajaxpost(query, item);
						}
					});
					conf.show();
				}else{
					sleajaxpost(query, item);
				}
				
			})
				
			function sleajaxpost (query, that){
				
				var target = that.attr("data-href");
				$.post(target, query).always(function() {
					
				}).done(function(data) {
					if (data.status == 1) {
						if (data.url) {
							$.scojs_message(data.info + ' 页面即将自动跳转~', $.scojs_message.TYPE_OK);
						} else {
							$.scojs_message(data.info, $.scojs_message.TYPE_OK);
						}
						setTimeout(function() {
							if (data.url) {
								location.href = data.url;
							} else if ($(that).hasClass('no-refresh')) {} else {
								location.reload();
							}
						}, 1500);
					} else {

						$.scojs_message(data.info, $.scojs_message.TYPE_OK);
						setTimeout(function() {
							if (data.url) {
								location.href = data.url;
							} else {}
						}, 1500);
					}
				});
			}

			//ajax get请求
			$('.ajax-get').click(function(ev) {
				
				ev.preventDefault();
				var target;
				var that = this;
			
				if ((target = $(this).attr('href')) || (target = $(this).attr('url'))) {
					
					if ($(this).hasClass('confirm')) {

						var conf = $.scojs_confirm({
							content: '确认要执行该操作吗?',
							action: function() {
								ajaxget(that, target);
							}
						});
						conf.show();
					}else{
						ajaxget(that, target);
					}
				}
				return false;
			});

			function ajaxget(that, target) {
					$(that).button("loading");
					$.get(target).always(function() {
						setTimeout(function(){
							$(that).button("reset");
						},1400);
					}).success(function(data) {
						if (data.status == 1) {
							if (data.url) {								
								$.scojs_message(data.info + ' 页面即将自动跳转~', $.scojs_message.TYPE_OK);
							} else {
								$.scojs_message(data.info, $.scojs_message.TYPE_OK);
							}
							setTimeout(function() {
								if (data.url) {
									location.href = data.url;
								} else if ($(that).hasClass('no-refresh')) {
								} else {
									location.reload();
								}
							}, 1500);
						} else {
							$.scojs_message(data.info, $.scojs_message.TYPE_OK);
							setTimeout(function() {
								if (data.url) {
									location.href = data.url;
								} else {
								}
							}, 1500);
						}
					});
				}
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
					if($.validator && (form.hasClass("validate-form") || form.hasClass("validateForm"))) {
						if(!form.valid()){
							$.scojs_message('表单验证不通过！',$.scojs_message.TYPE_ERROR);
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


					} else if (form.get(0).nodeName == 'INPUT' || form.get(0).nodeName == 'SELECT' || form.get(0).nodeName == 'TEXTAREA') {
						//以input 为触发节点
						form.each(function(k, v) {
							if (v.type == 'checkbox' && v.checked == true) {
								nead_confirm = true;
							}
						})
						query = form.serialize();

					} else {

						query = form.find('input,select,textarea').serialize();
						
					}


				}
				
				if ($(this).hasClass('confirm')) {
					$(this).scojs_confirm({
						content: '确认要执行该操作吗',
						action: function() {
							myUtils.ajaxpost(that, target, query);
						}
					}).show();
				} else {
					myUtils.ajaxpost(that, target, query);
				}
				return false;
			}); //END ajax-post
			

			$(window).resize();
		}) //end $.ready
		
		$(function () {
		  $('[data-toggle="tooltip"]').tooltip();
		  $('[data-toggle="popover"]').popover();
		})


})(jQuery, window);