$(function() {


	var main = {

		method: "",

		//初始化方法  页面打开就要执行初始化
		init: function() {
			//初始化时执行main中的方法（功能）
			this.nav();
			this.diy_radio();
			this.diy_select();
			this.route();
			this.select_year();
			this.select_month();
			this.select_day();
			this.select_edu();

			//轮播图
			this.lunbo();



		},

		lunbo:function(){
			
			//1.初始化获取元素
			var speed = 100;
			var tab = document.getElementsByClassName('slide_info')[0];
			var tab1 = document.getElementsByClassName('list1')[0];
			var tab2 = document.getElementsByClassName('list2')[0];
			tab2.innerHTML = tab1.innerHTML;

			function Marquee() {
				if (tab2.offsetWidth - tab.scrollLeft <= 0)
					tab.scrollLeft -= tab1.offsetWidth;
				else {
					tab.scrollLeft++;
				}
			}
			var MyMar = setInterval(Marquee, speed);
			tab.onmouseover = function() {
				clearInterval(MyMar)
			};
			tab.onmouseout = function() {
				MyMar = setInterval(Marquee, speed)
			};

		},
		
		nav: function() {
			//1.获取参数字符串
			var url = location.href;

			//2.通过正则获取info控制器后面的方法名称,然后匹配
			var reg = /.*?info\/(\w+)/;
			var res = url.match(reg);
			//获取匹配结果里面的索引值为1的元素 ,没有取到则按默认的index
			var method = res ? res[1] : 'index';

			//路由使用
			this.method = method;

			//3.根据匹配到的方法名查找对应的链接   href属性中有该方法名的	
			var li = $(".sidebar a[href*=" + method + "]").parent();

			//如果没取到的话默认使用带index的li
			if (!li.length) li = $('.sidebar li:eq(2)');

			//4.给被选中的导航链接添加类名
			li.addClass('on');

		},
		//自定义单选按钮功能
		diy_radio: function() {
			//通过事件委托，来添加单击事件--------!!!!!
			$(document).on('click', '.radio p', function() {
				$(this).addClass('checked').siblings().removeClass('checked');

				//模拟复选框的功能
				//				$(this).toggleClass('checked');
			})

		},

		//自定义下拉列表按钮功能
		diy_select: function() {
			//通过事件委托，来添加单击事件显示下拉框--------!!!!!
			$(document).on('click', '.select .tip', function() {
				$(this).next('ul').show().css('z-index', 9999);
			})

			//鼠标单击获取li的值
			$(document).on('click', '.select li', function() {
				var txt = $(this).html();
				var u = $(this).parent().prev().children('u');
				u.html(txt).attr('data-value', $(this).attr('data-value'));

			})

			//鼠标离开后隐藏下拉列表
			$('.select').mouseleave(function() {
				$(this).children('ul').hide();

			})
		},

		//路由方法
		route: function() {
			var self = this;
			var method = this.method;
			if ($.isFunction(self[method])) {
				self[method]();
			}
		},

		//首页方法index
		index: function() {
			//先获取数据
			$.ajax({
				type: "get",
				url: "../api/base_info",
				success: function(txt) {
					var data = $.parseJSON(txt);
					var res = data.result;

					//注意 数据中的属性名与表单的name属性有对应关系

					//1.遍历数据
					$.each(res, function(i) {
						//						console.log(i);//属性名
						var input = $('.form input[name=' + i + '],.form textarea[name=' + i + ']');
						//更新值此时this=res[i]属性值
						input.val(this);
					})

					//2.获取单选按钮
					var radio = $('.form .radio p');
					radio.eq(res.sex).addClass('checked').siblings().removeClass('checked');

					//3.获取时间
					var zhm_time = new Date(res.birthday * 1000);
					var year = zhm_time.getFullYear();
					var month = zhm_time.getMonth() + 1;
					var zhm_date = zhm_time.getDate();

					$('.select.year u').html(year);
					$('.select.month u').html(month);
					$('.select.day u').html(zhm_date);


					//4.设置真实姓名一经确定不可修改
					if (res.real_name) {
						$('.form input[name=real_name]').replaceWith('<span class="real_name">' + res.real_name + '</span>');

					}
				}
			});

			//5.点击save保存个人信息
			$('.save').click(function() {
				//定义对象保存数据
				var data = {};
				//遍历表单的值
				$('.form input,.form textarea').each(function() {
						//将当前表单对象的name值作为data的属性名,当前表单的值就作为value
						data[this.name] = this.value;
					})
					//将真实姓名保存到数据data中
				if ($('.form .real_name').length) {
					data.real_name = $(".form .real_name").val();
				} else {
					data.real_name = $('.form input[name=real_name]').val();
				}
				//获取被选中的性别，它的索引值是几。性别就是几。男0女1
				data.sex = $('.form .radio .checked').index();
				//将生日数据转换为时间戳存放到data中
				var time = $('.select.year u').html() + "/" + $('.select.month u').html() + "/" + $('.select.day u').html();
				//转换为时间戳
				time = new Date(time).getTime() / 1000;
				data.birthday = time;

				//发送ajax请求
				$.ajax({
					type: "post",
					url: "../api/set_base_info",
					data: data,
					success: function(txt) {
						var dataJSON = $.parseJSON(txt);
						if (dataJSON.error) {
							alert(dataJSON.error);
						} else {
							//设置real_name
							$('.form input[name=real_name]').replaceWith('<span class="real_name">' + data.real_name + '</span>');
							alert("保存成功")
						}
					}
				});


			})


		},


		//绑定手机
		phone: function() {
			//获取form的所有input框
			var inputs = $('.form input');


			//给按钮添加事件
			$('.save').click(function() {
				//获取输入的手机号
				var phone = inputs.eq(0).val();

				$.ajax({
					type: "post",
					url: "../api/phone",
					data: {
						phone: phone
					},
					success: function(txt) {
						var data = $.parseJSON(txt);
						if (data.error) {
							alert(data.error);
						} else {
							alert('绑定成功');
							inputs.eq(0).val("");
						}
					}
				});
			})
		},


		email: function() {
			//获取form的所有input框
			var inputs = $('.form input');
			//给按钮添加事件
			$('.save').click(function() {
				//获取输入的手机号
				var email = inputs.eq(0).val();

				$.ajax({
					type: "post",
					url: "../api/email",
					data: {
						email: email
					},
					success: function(txt) {
						var data = $.parseJSON(txt);
						if (data.error) {
							alert(data.error);
						} else {
							alert('绑定成功');
							inputs.eq(0).val("");
						}
					}
				});

			})
		},


		pwd: function() {
			//获取form的所有input框
			var inputs = $('.form input');
			//给按钮添加事件
			$('.save').click(function() {
				//获取输入的手机号
				var pwd = inputs.eq(0).val();
				var pwd1 = inputs.eq(1).val();
				var pwd2 = inputs.eq(2).val();

				$.ajax({
					type: "post",
					url: "../api/pwd",
					data: {
						pwd: pwd,
						pwd1: pwd1,
						pwd2: pwd2
					},
					success: function(txt) {
						var data = $.parseJSON(txt);
						if (data.error) {
							alert(data.error);
						} else {
							alert('修改成功');
							inputs.val("");
						}
					}
				});


			})
		},


		//年月日的设置
		select_year: function() {
			//获取下拉框里的元素
			var ul = $('.select.year').children('ul');
			var html = "<li>-年-</li>";
			var start = 1980;
			var end = new Date().getFullYear();
			for (; start <= end; start++) {
				html += "<li>" + start + "</li>";
			}

			ul.html(html);
			ul.prev().children('u').html('-年-');


		},
		select_month: function() {
			//获取下拉框里的元素
			var ul = $('.select.month').children('ul');
			var html = "<li>-月-</li>";
			var start = 1;
			var end = 12;
			for (; start <= end; start++) {
				html += "<li>" + start + "</li>";
			}
			ul.html(html);
			ul.prev().children('u').html('-月-');

			//触发日期函数
			//main.select_day();
		},
		select_day: function() {
			//获取下拉框里的元素
			var ul = $('.select.day').children('ul');
			var html = "<li>-日-</li>";
			var start = 1;
			var end = 31;
			for (; start <= end; start++) {
				html += "<li>" + start + "</li>";
			}
			ul.html(html);
			ul.prev().children('u').html('-日-');

			/*//对年月日做判断-----------修改：点击年月之后再触发函数
			//添加之前先初始化
			var ul = $('.select.day').children('ul')
			var year = $('.year .tip').children('u').html();
			var month = $('.month .tip').children('u').html();
			
			//判断是否为空
			if(year == "" || month == ""){
				return;
			}else{
				var arr = new Array(31,28,31,30,31,30,31,31,30,31,30,31);
				//判断是否为闰年
				if((year%4 == 0&&year%100==0)||year%400==0){
					arr[1]++;
				}
				var html = "";
				for(var i = 1;i<=arr[month-1];i++){
					html+="<li>"+i+"</li>";
				}
				ul.html(html);
			}*/

		},
		//教育信息页面的方法
		edu: function() {
			var eduList = "幼儿园 小学 初中 高中 大学 加里敦".split(/\s+/); //使用正则将string分隔开
			//获取数据显示
			$.ajax({
				type: "get",
				url: "../api/edu_list",
				success: function(txt) {
					var data = $.parseJSON(txt);
					var res = data.result;
					var html = '';
					//遍历数据
					$.each(res, function(i) {
						var tmp = this;
						html += '<tr><td>' + eduList[tmp.type] + '</td><td>' + tmp.title + '</td><td>' + new Date(tmp.year * 1000).getFullYear() + '</td><td><a href="javascript:;" data-id="' + tmp.id + '">删除</a></td></tr>';

					})

					$('.data').append(html);
					//console.log(res);
				}
			});


			//保存数据，并提交
			$('.save').click(function() {

				//定义一个对象保存数据
				var data = {};

				data.type = $('.select.edu u').attr('data-value');

				data.year = new Date($('.select.year u').html()).getTime() / 1000;
				data.title = $('.form input').val();
				//验证一下
				if (data.type == undefined) return alert("请选择学历！");
				if (isNaN(data.year)) return alert('请选择正确的时间！');
				if (data.title.length < 4) return alert('请输入正确的学校名称！');



				//使用ajax发送数据
				$.ajax({
					type: "post",
					url: "../api/add_edu",
					data: data,
					success: function(txt) {
						var dataJson = $.parseJSON(txt);
						if (dataJson.error) {
							alert(dataJson.error);
						} else {
							$('.data tr:first').after('<tr><td>' + eduList[data.type] + '</td><td>' + data.title + '</td><td>' + new Date(data.year).getFullYear() + '</td><td><a href="javascript:;" data-id="' + data.id + '">删除</a></td></tr>');

						}
					}
				});

			});

			//删除数据，事件委托
			$('.data').on('click', 'a', function() {
				var id = $(this).attr('data-id');
				var tr = $(this).parent().parent();

				$.ajax({
					type: "get",
					url: "../api/del_edu",
					data: {
						id: id
					},
					success: function(txt) {
						var data = $.parseJSON(txt);
						if (data.error) {
							alert(data.error)
						} else {
							tr.remove();
						}
					}
				})

			})

		},
		select_edu: function() {
			//获取下拉框里的元素
			var ul = $('.select.edu').children('ul');
			var html = "<li>-选择学历-</li>";
			var eduList = '幼儿园   小学    初中     高中     大学          家里蹲'.split(/\s+/);
			$.each(eduList, function(i) {
				html += '<li data-value="' + i + '">' + this + '</li>';
			});
			ul.html(html);
			ul.prev().children('u').html('-选择学历-');
		},


		//工作经历页面
		//教育信息页面的方法
		job: function() {
			//从页面获取数据

			//获取数据显示
			$.ajax({
				type: "get",
				url: "../api/job_list",
				success: function(txt) {
					var data = $.parseJSON(txt);
					var res = data.result;
					console.log(res);
					var html = '';
					//遍历数据
					$.each(res, function(i) {
						var tmp = this;
						var time = new Date(tmp.start * 1000).getFullYear() + "-" + (new Date(tmp.start * 1000).getMonth() - 0 + 1) + "-1到" + new Date(tmp.end * 1000).getFullYear() + "-" + (new Date(tmp.end * 1000).getMonth() - 0 + 1) + "-1";
						html += '<tr><td>' + tmp.firm + '</td><td>' + tmp.sector + tmp.title + '</td><td>' + time + '</td><td><a href="javascript:;" data-id="' + tmp.id + '">删除</a></td></tr>';


					})

					$('.data').append(html);
					//console.log(html);
				}
			});




			//保存数据，并提交
			$('.save').click(function() {

				//定义一个对象保存数据
				var data = {};
				data.firm = $('.job input[name=firm]').val();
				data.sector = $('.job input[name=sector]').val();
				data.title = $('.job input[name=title]').val();
				//data.address = $('.job input[name=address]').val();
				data.start = new Date($('.year.y1 u').html() + '/' + $('.month.m1 u').html() + '/1').getTime() / 1000;
				data.end = new Date($('.year.y2 u').html() + '/' + $('.month.m2 u').html() + '/1').getTime() / 1000;


				//使用ajax发送数据
				$.ajax({
					type: "post",
					url: "../api/add_job",
					data: data,
					success: function(txt) {
						var dataJson = $.parseJSON(txt);
						if (dataJson.error) {
							console.log(dataJson.error);
						} else {
							var time = new Date(data.start * 1000).getFullYear() + "-" + (new Date(data.start * 1000).getMonth() + 0 + 1) + "-1到" + new Date(data.end * 1000).getFullYear() + "-" + (new Date(data.end * 1000).getMonth() + 0 + 1) + "-1";

							$('.data tr:first').after('<tr><td>' + data.firm + '</td><td>' + data.sector + data.title + '</td><td>' + time + '</td><td><a href="javascript:;" data-id="' + data.id + '">删除</a></td></tr>');

						}
					}
				});

			});

			//删除数据，事件委托
			$('.data').on('click', 'a', function() {
				var id = $(this).attr('data-id');
				var tr = $(this).parent().parent();

				$.ajax({
					type: "get",
					url: "../api/del_job",
					data: {
						id: id
					},
					success: function(txt) {
						var data = $.parseJSON(txt);
						if (data.error) {
							alert(data.error)
						} else {
							tr.remove();
						}
					}
				})

			})

		},
		feat: function() {

			//获取数据显示
			$.ajax({
				type: "get",
				url: "../api/feat_list",
				success: function(txt) {
					var data = $.parseJSON(txt);
					var res = data.result;
					var html = "";
					$.each(res, function() {
						var tmp = this;
						html += "<a href='javascript:;'>" + tmp.title + "<b data-id='" + tmp.id + "'>×</b></a>";
					});
					$('.feats').append(html);
				}
			});

			//点击save保存数据
			//保存数据，并提交
			$('.save').click(function() {

				//定义一个对象保存数据
				var data = {};
				data.title = $('input[name=feat_title]').val();

				//使用ajax发送数据
				$.ajax({
					type: "post",
					url: "../api/add_feat",
					data: data,
					success: function(txt) {
						var dataJSON = $.parseJSON(txt);
						if (dataJSON.error) return alert(dataJSON.error);
						$('.feats').append('<a href="javascript:;">' + data.title + '<b data-id="' + dataJSON.id + '">&times;</b></a>');

						//$('.f_right input').inn
					}
				});

			});

			//删除数据，事件委托
			$('.feats').on('click', 'b[data-id]', function() {
				var id = $(this).attr('data-id');
				var a = $(this).parent();
				$.ajax({
					type: "get",
					url: "../api/del_feat",
					data: {
						id: id
					},
					success: function(txt) {
						var dataJSON = $.parseJSON(txt);
						if (dataJSON.error) {
							alert(dataJSON.error)
						} else {
							a.remove();
						}

					}
				})

			});


		},
		uploadPic: function() {
			//1.读取最近使用的头像列表

			$.ajax({
				type: "get",
				url: "../api/icon_list",
				success: function(txt) {

					var dataJSON = $.parseJSON(txt);
					var data = dataJSON.result;

					//更新最近使用的头像的三张照片

					$('.list img').each(function(i) {
						$(this).attr({
							src: 'upload/' + data[i].src,
							'data-id': data[i].id
						});
					})

					//更新当前的头像的地址

					$('.user_icon,.icon_r>img').attr({
						'src': 'upload/' + dataJSON.cur.src,
						'data-id': dataJSON.cur.id
					})

				}
			});

			//给最近使用的头像添加单击事件,然后显示在待选头像的框中
			$('.list img').click(function() {
				var src = this.src;
				var id = $(this).attr('data-id');
				$('.user_icon,.icon_r>img').attr({
					'src': src,
					'data-id': id
				});

			})


			//点击save按钮，将信息保存到数据库
			$('.save').click(function() {
				$.ajax({
					type: "get",
					url: "../api/set_icon",
					data: {
						id: $('.user_icon').attr('data-id')
					},
					success: function(txt) {
						var data = $.parseJSON(txt);
						if (data.error) {
							alert(data.error);
						} else {
							alert('保存成功');
							console.log(data);
						}


					}
				});





			})



			//1.引入无刷新 上传 插件
			$('body').append('<script src="common/js/jquery.uploadify.min.js"></script>');
			//2.选择一个元素绑定上传按钮
			$('#upload_icon').uploadify({

				//2.1设置动画地址
				swf: 'common/js/uploadify.swf',
				//指定上传地址
				uploader: '../api/upload_icon',
				//设置按钮的类名
				buttonClass: 'upload_btn',
				//设置按钮的宽高
				width: 220,
				height: 40,

				//设置按钮上的文字 
				buttonText: '上传头像',
				// 设置发送给后台时 文件的name值  input type="file" name="icon"	
				fileObjName: 'icon',
				//上传成功执行的回调函数
				onUploadSuccess: function(a, txt) {
					//第一个参数是传的对象，第二个参数是服务器返回的数据
					console.log(a);

					var data = $.parseJSON(txt);
					if (data.error) {
						alert(data.error)
					} else {
						console.log('成功')
					}
					console.log(txt)
				}


			});


		}

	}

	main.init();

})