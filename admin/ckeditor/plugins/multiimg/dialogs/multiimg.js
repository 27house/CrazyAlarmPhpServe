(function() {
    CKEDITOR.dialog.add("multiimg",
        function(a) {
            return {
                title: "批量上传图片",
                minWidth: "660px",
                minHeight:"400px",
                contents: [{
                    id: "tab1",
                    label: "",
                    title: "",
                    expand: true,
                    width: "420px",
                    height: "300px",
                    padding: 0,
                    elements: [{
                        type: "html",
                        style: "width:660px;height:400px",
                        html: '<iframe name="uploadFrame" id="uploadFrame" src="ckeditor/plugins/multiimg/image/image.html?v=' +new Date().getSeconds() + '" frameborder="0"></iframe>'
                    }]
                }],
                onOk: function() {
                    //点击确定按钮后的操作
					$html = window.frames["uploadFrame"].document.getElementById("cs").innerHTML;
					if($html != ''){
					  a.insertHtml($html);
					}else{
						alert('您还没有上传图片！');
						return false;
					}
                  
					
                },
                onShow: function () {
                    document.getElementById("uploadFrame").setAttribute("src","ckeditor/plugins/multiimg/image/image.html?v=' +new Date().getSeconds() + '");
                }
            }
        })
})();