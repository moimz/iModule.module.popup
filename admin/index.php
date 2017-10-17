<?php
/**
 * 이 파일은 iModule 팝업모듈 일부입니다. (https://www.imodule.kr)
 *
 * 홈페이지 내 팝업창과 관련된 전반적인 기능을 관리한다.
 * 
 * @file /modules/popup/admin/index.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0.160910
 */
if (defined('__IM__') == false) exit;
?>
<script>
Ext.onReady(function () { Ext.getCmp("iModuleAdminPanel").add(
	new Ext.TabPanel({
		id:"ModulePopup",
		border:false,
		tabPosition:"bottom",
		items:[
			new Ext.grid.Panel({
				id:"ModulePopupList",
				title:"팝업관리",
				border:false,
				layout:"fit",
				tbar:[
					new Ext.Button({
						text:"팝업생성",
						iconCls:"xi xi-windows-add",
						handler:function() {
							Popup.add();
						}
					}),
					"-",
					new Ext.Button({
						text:"선택팝업삭제",
						iconCls:"mi mi-trash",
						handler:function() {
							Popup.delete();
						}
					})
				],
				store:new Ext.data.JsonStore({
					proxy:{
						type:"ajax",
						simpleSortMode:true,
						url:ENV.getProcessUrl("popup","@getPopups"),
						extraParams:{type:"",keyword:""},
						reader:{type:"json"}
					},
					remoteSort:true,
					sorters:[{property:"start_date",direction:"DESC"}],
					autoLoad:true,
					pageSize:50,
					fields:[],
					listeners:{
						load:function(store,records,success,e) {
							if (success == false) {
								if (e.getError()) {
									Ext.Msg.show({title:Admin.getText("alert/error"),msg:e.getError(),buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR})
								} else {
									Ext.Msg.show({title:Admin.getText("alert/error"),msg:Admin.getText("error/load"),buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR})
								}
							}
						}
					}
				}),
				columns:[{
					text:"게시사이트",
					dataIndex:"site",
					minWidth:150,
					flex:1
				},{
					text:"팝업명",
					dataIndex:"title",
					width:180
				},{
					text:"종류",
					dataIndex:"type",
					width:120,
					align:"center",
					renderer:function(value) {
						return Popup.getText("type/"+value);
					}
				},{
					text:"팝업형태",
					dataIndex:"window",
					width:80,
					align:"center",
					renderer:function(value) {
						return Popup.getText("window/"+value);
					}
				},{
					text:"크기",
					dataIndex:"size",
					width:100,
					align:"center",
					renderer:function(value) {
						var temp = value.split(",");
						return temp[0] + " x " + temp[1] + "px";
					}
				},{
					text:"게시시작일",
					dataIndex:"start_date",
					width:120,
					align:"center",
					renderer:function(value) {
						return moment(value * 1000).locale($("html").attr("lang")).format("YYYY-MM-DD(dd)");
					}
				},{
					text:"게시종료일",
					dataIndex:"end_date",
					width:120,
					align:"center",
					renderer:function(value) {
						return moment(value * 1000).locale($("html").attr("lang")).format("YYYY-MM-DD(dd)");
					}
				},{
					text:"등록일",
					dataIndex:"reg_date",
					width:140,
					align:"center",
					renderer:function(value) {
						return moment(value * 1000).locale($("html").attr("lang")).format("YYYY-MM-DD HH:mm");
					}
				}],
				selModel:new Ext.selection.CheckboxModel(),
				bbar:new Ext.PagingToolbar({
					store:null,
					displayInfo:false,
					items:[
						"->",
						{xtype:"tbtext",text:Admin.getText("text/grid_help")}
					],
					listeners:{
						beforerender:function(tool) {
							tool.bindStore(Ext.getCmp("ModulePopupList").getStore());
						}
					}
				}),
				listeners:{
					itemdblclick:function(grid,record) {
						Popup.add(record.data.idx);
					},
					itemcontextmenu:function(grid,record,item,index,e) {
						var menu = new Ext.menu.Menu();
						
						menu.add('<div class="x-menu-title">'+record.data.title+'</div>');
						
						menu.add({
							iconCls:"xi xi-windows",
							text:"팝업 미리보기",
							handler:function() {
								var temp = record.data.size.split(",");
								iModule.openPopup(ENV.getModuleUrl("popup","window",record.data.idx),temp[0],temp[1],0);
							}
						});
						
						menu.add("-");
						
						menu.add({
							iconCls:"xi xi-form",
							text:"팝업 수정",
							handler:function() {
								Popup.add(record.data.idx);
							}
						});
						
						menu.add({
							iconCls:"mi mi-trash",
							text:"팝업 삭제",
							handler:function() {
								Popup.delete(record.data.idx);
							}
						});
						
						e.stopEvent();
						menu.showAt(e.getXY());
					}
				}
			})
		]
	})
); });
</script>