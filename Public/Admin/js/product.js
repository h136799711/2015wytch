function queryGroups() {
	//			console.log("queryGroups");
	$.ajax({
		type: "post",
		url: window.ServicesURL.groupGetAll + '?rand=' + (new Date()).getTime(),
		beforeSend: function(XMLHttpRequest) {
			$(".js_group .loading").show();
			$(".js_group .shop-group-list").empty();
		}
	}).done(function(data) {
//		console.log(data);
		if (data.status) {
			if (data.info) {
				$cont = $(".shop-group-list");
				data = data.info;
				for (var i = 0; i < data.length; i++) {

					var html = '<input type="checkbox" name="groups[]" id="group_' + data[i].group_id + '"  value="' + data[i].group_id + '"  />' + data[i].group_name;
					$ele = $('<label class="checkbox-inline"></label>').html(html);
					$cont.append($ele);
				}

			}

		} else {

		}
	}).always(function() {
		$(".js_group .loading").hide();
	});
}


//商品分组处理
function productGroups() {
	setTimeout(queryGroups, 500);
	$(".js_group .js_group_rfs").click(function() {
		queryGroups();
	});

	$(".js_group .js_group_new_cancel").click(function() {
		$(".js_group .js_group_new_form").addClass("hidden");
	});

	$(".js_group .js_group_new").click(function() {
		$(".js_group .js_group_new_form").removeClass("hidden");
	});
	$(".js_group .js_group_new_submit").click(function() {
		var group_name = $(".js_group .js_group_name").val();
		var len = group_name.replace(/[^\x00-\xff]/g, 'xx').length;
		if (len > 10 || len <= 0) {
			$.scojs_message('分组名称应为1-5个字', $.scojs_message.TYPE_ERROR);
		}
		$.post(window.ServicesURL.groupAdd, {
			group_name: group_name
		}).done(function(data) {
			//
			if (data.status) {
				$.scojs_message('添加成功!', $.scojs_message.TYPE_OK);
				$(".js_group .js_group_new_cancel").click();
				console.log(data.info);

				cont = $(".shop-group-list");
				var html = '<input type="checkbox" id="group_' + data.info + '" name="groups[]"  value="' + data.info + '"  />' + group_name;
				$ele = $('<label class="checkbox-inline"></label>').html(html);
				$cont.append($ele);

				//						setTimeout(queryGroups,1000);
				$(".js_group .js_group_name").val("");
			} else {
				$.scojs_message('添加失败!', $.scojs_message.TYPE_ERROR);
			}
		});
		//TODO:检测分组名称
	});
}

