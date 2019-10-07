/**
 * 이 파일은 iModule 팝업모듈 일부입니다. (https://www.imodules.io)
 *
 * 팝업모듈 관리자 UI를 구성한다.
 * 
 * @file /modules/popup/admin/scripts/script.js
 * @author Arzz (arzz@arzz.com)
 * @license GPLv3
 * @version 3.1.0
 * @modified 2019. 10. 7.
 */
var Popup = {
	/**
	 * 팝업 목록관리
	 */
	list:{
		/**
		 * 팝업 추가/삭제
		 * 
		 * @param {string} idx 팝업 id
		 */
		add:function(idx) {
			new Ext.Window({
				id:"ModulePopupAddWindow",
				title:(idx ? "팝업수정" : "팝업생성"),
				width:700,
				modal:true,
				autoScroll:true,
				border:false,
				items:[
					new Ext.form.Panel({
						id:"ModulePopupAddForm",
						border:false,
						bodyPadding:"10 10 10 10",
						fieldDefaults:{labelAlign:"right",labelWidth:100,anchor:"100%",allowBlank:false},
						items:[
							new Ext.form.Hidden({
								name:"idx"
							}),
							new Ext.form.FieldSet({
								title:"기본설정",
								items:[
									new Ext.form.TextField({
										name:"title",
										fieldLabel:"팝업명"
									}),
									new Ext.form.ComboBox({
										fieldLabel:"게시사이트",
										name:"site",
										store:new Ext.data.JsonStore({
											proxy:{
												type:"ajax",
												url:ENV.getProcessUrl("popup","@getSites"),
												reader:{type:"json"}
											},
											remoteSort:false,
											autoLoad:true,
											sorters:[{property:"sort",direction:"ASC"}],
											fields:["display","value"],
											listeners:{
												load:function(store) {
													if (idx === undefined && store.getCount() == 1) {
														Ext.getCmp("ModulePopupAddForm").getForm().findField("site").setValue(store.getAt(0).get("value"));
														Ext.getCmp("ModulePopupAddForm").getForm().findField("site").hide();
													}
												}
											}
										}),
										displayField:"display",
										valueField:"value"
									}),
									new Ext.form.FieldContainer({
										fieldLabel:"팝업게시일",
										layout:"hbox",
										items:[
											new Ext.form.DateField({
												name:"start_date",
												format:"Y-m-d",
												width:130,
												value:moment().format("YYYY-MM-DD")
											}),
											new Ext.form.DisplayField({
												value:"~",
												style:{marginLeft:"5px",marginRight:"5px"}
											}),
											new Ext.form.DateField({
												name:"end_date",
												format:"Y-m-d",
												width:130,
												value:moment().add(1,"w").format("YYYY-MM-DD")
											}),
											new Ext.form.DisplayField({
												flex:1
											}),
											new Ext.Button({
												text:"1일",
												style:{marginLeft:"2px"},
												handler:function() {
													var start_date = moment(Ext.getCmp("ModulePopupAddForm").getForm().findField("start_date").getValue());
													Ext.getCmp("ModulePopupAddForm").getForm().findField("end_date").setValue(start_date.add(1,"day").format("YYYY-MM-DD"));
												}
											}),
											new Ext.Button({
												text:"3일",
												style:{marginLeft:"2px"},
												handler:function() {
													var start_date = moment(Ext.getCmp("ModulePopupAddForm").getForm().findField("start_date").getValue());
													Ext.getCmp("ModulePopupAddForm").getForm().findField("end_date").setValue(start_date.add(3,"day").format("YYYY-MM-DD"));
												}
											}),
											new Ext.Button({
												text:"7일",
												style:{marginLeft:"2px"},
												handler:function() {
													var start_date = moment(Ext.getCmp("ModulePopupAddForm").getForm().findField("start_date").getValue());
													Ext.getCmp("ModulePopupAddForm").getForm().findField("end_date").setValue(start_date.add(7,"day").format("YYYY-MM-DD"));
												}
											}),
											new Ext.Button({
												text:"15일",
												style:{marginLeft:"2px"},
												handler:function() {
													var start_date = moment(Ext.getCmp("ModulePopupAddForm").getForm().findField("start_date").getValue());
													Ext.getCmp("ModulePopupAddForm").getForm().findField("end_date").setValue(start_date.add(15,"day").format("YYYY-MM-DD"));
												}
											}),
											new Ext.Button({
												text:"30일",
												style:{marginLeft:"2px"},
												handler:function() {
													var start_date = moment(Ext.getCmp("ModulePopupAddForm").getForm().findField("start_date").getValue());
													Ext.getCmp("ModulePopupAddForm").getForm().findField("end_date").setValue(start_date.add(30,"day").format("YYYY-MM-DD"));
												}
											})
										]
									}),
									new Ext.form.ComboBox({
										fieldLabel:"팝업종류",
										name:"type",
										store:new Ext.data.ArrayStore({
											fields:["display","value"],
											data:[["내용 직접입력","HTML"],["이미지","IMAGE"],["외부 URL","LINK"],["외부 파일","EXTERNAL"]]
										}),
										editable:false,
										displayField:"display",
										valueField:"value",
										value:"HTML",
										listeners:{
											change:function(form,value) {
												Ext.getCmp("ModulePopupAdd-HTML").disable();
												Ext.getCmp("ModulePopupAdd-HTML").hide();
												
												Ext.getCmp("ModulePopupAdd-"+value).enable();
												Ext.getCmp("ModulePopupAdd-"+value).show();
												
												if (value == "IMAGE") {
													Ext.getCmp("ModulePopupAddSize").disable();
													Ext.getCmp("ModulePopupAddSize").hide();
												} else {
													Ext.getCmp("ModulePopupAddSize").enable();
													Ext.getCmp("ModulePopupAddSize").show();
												}
											}
										}
									}),
									new Ext.form.ComboBox({
										fieldLabel:"팝업형태",
										name:"window",
										store:new Ext.data.ArrayStore({
											fields:["display","value"],
											data:[["팝업창","WINDOW"],["레이어팝업","LAYER"]]
										}),
										editable:false,
										displayField:"display",
										valueField:"value",
										value:"WINDOW"
									})
								]
							}),
							new Ext.form.FieldSet({
								id:"ModulePopupAdd-HTML",
								title:"팝업내용입력",
								items:[
									Admin.wysiwygField("","html")
								]
							}),
							new Ext.form.FieldSet({
								id:"ModulePopupAdd-IMAGE",
								title:"이미지설정",
								disabled:true,
								hidden:true,
								items:[
									new Ext.form.FileUploadField({
										fieldLabel:"팝업이미지",
										name:"image",
										buttonText:"찾아보기",
										accept:"image/*",
										allowBlank:idx !== undefined,
										afterBodyEl:'<div id="ModulePopupAddImageHelp" class="x-form-help">첨부된 이미지크기에 맞게 팝업사이즈가 자동으로 결정됩니다.</div>'
									}),
									new Ext.form.TextField({
										fieldLabel:"이동 URL",
										name:"image_link",
										allowBlank:true,
										afterBodyEl:'<div class="x-form-help">이미지클릭시 이동할 URL이 있다면 URL주소를 입력하여 주십시오.</div>'
									})
								]
							}),
							new Ext.form.FieldSet({
								id:"ModulePopupAdd-LINK",
								title:"외부 URL 설정",
								hidden:true,
								disabled:true,
								items:[
									new Ext.form.TextField({
										fieldLabel:"외부 URL",
										name:"link",
										afterBodyEl:'<div class="x-form-help">팝업창에 표시될 URL 을 입력하여 주십시오.</div>'
									})
								]
							}),
							new Ext.form.FieldSet({
								id:"ModulePopupAdd-EXTERNAL",
								title:"외부파일설정",
								hidden:true,
								disabled:true,
								items:[
									new Ext.form.ComboBox({
										name:"external",
										store:new Ext.data.JsonStore({
											proxy:{
												type:"ajax",
												url:ENV.getProcessUrl("admin","@getExternals"),
												reader:{type:"json"}
											},
											autoLoad:true,
											remoteSort:false,
											sorters:[{property:"path",direction:"ASC"}],
											fields:["path","display"]
										}),
										displayField:"display",
										valueField:"path",
										afterBodyEl:'<div class="x-form-help">팝업창에 표시될 외부파일을 선택하여 주십시오.</div>'
									}),
								]
							}),
							new Ext.form.FieldSet({
								title:"팝업크기/위치 설정",
								items:[
									new Ext.form.FieldContainer({
										id:"ModulePopupAddSize",
										fieldLabel:"크기(가로x세로)",
										layout:"hbox",
										items:[
											new Ext.form.NumberField({
												name:"width",
												width:130,
												value:400,
												minValue:0,
												maxValue:1000
											}),
											new Ext.form.DisplayField({
												value:"X",
												style:{marginLeft:"5px",marginRight:"5px"}
											}),
											new Ext.form.NumberField({
												name:"height",
												width:130,
												value:400,
												minValue:0,
												maxValue:1000
											})
										]
									}),
									new Ext.form.FieldContainer({
										id:"ModulePopupAddPosition",
										fieldLabel:"위치(X,Y)",
										layout:"hbox",
										items:[
											new Ext.form.NumberField({
												name:"x",
												width:130,
												value:0,
												minValue:0,
												maxValue:1000,
												disabled:true
											}),
											new Ext.form.DisplayField({
												value:"X",
												style:{marginLeft:"5px",marginRight:"5px"}
											}),
											new Ext.form.NumberField({
												name:"y",
												width:130,
												value:0,
												minValue:0,
												maxValue:1000,
												disabled:true
											}),
											new Ext.form.DisplayField({
												flex:1
											}),
											new Ext.form.Checkbox({
												boxLabel:"자동설정",
												name:"auto_position",
												checked:true,
												listeners:{
													change:function(form,value) {
														form.getForm().findField("x").setDisabled(value);
														form.getForm().findField("y").setDisabled(value);
													}
												}
											})
										],
										afterBodyEl:'<div class="x-form-help">팝업창의 위치를 모니터 좌상단 기준으로 지정합니다.<br>자동으로 설정할 경우 가급적 팝업이 겹쳐지지 않는 범위내에서 자동으로 위치를 결정합니다.</div>'
									})
								]
							})
						]
					})
				],
				buttons:[
					new Ext.Button({
						text:Admin.getText("button/confirm"),
						handler:function() {
							Ext.getCmp("ModulePopupAddForm").getForm().submit({
								url:ENV.getProcessUrl("popup","@savePopup"),
								submitEmptyText:false,
								waitTitle:Admin.getText("action/wait"),
								waitMsg:Admin.getText("action/saving"),
								success:function(form,action) {
									Ext.Msg.show({title:Admin.getText("alert/info"),msg:Admin.getText("action/saved"),buttons:Ext.Msg.OK,icon:Ext.Msg.INFO,fn:function() {
										Ext.getCmp("ModulePopupList").getStore().loadPage(1);
										Ext.getCmp("ModulePopupAddWindow").close();
									}});
								},
								failure:function(form,action) {
									if (action.result) {
										if (action.result.message) {
											Ext.Msg.show({title:Admin.getText("alert/error"),msg:action.result.message,buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
										} else {
											Ext.Msg.show({title:Admin.getText("alert/error"),msg:Admin.getErrorText("DATA_SAVE_FAILED"),buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
										}
									} else {
										Ext.Msg.show({title:Admin.getText("alert/error"),msg:Admin.getErrorText("INVALID_FORM_DATA"),buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
									}
								}
							});
						}
					}),
					new Ext.Button({
						text:Admin.getText("button/cancel"),
						handler:function() {
							Ext.getCmp("ModulePopupAddWindow").close();
						}
					})
				],
				listeners:{
					show:function() {
						if (idx) {
							Ext.getCmp("ModulePopupAddForm").getForm().load({
								url:ENV.getProcessUrl("popup","@getPopup"),
								params:{idx:idx},
								waitTitle:Admin.getText("action/wait"),
								waitMsg:Admin.getText("action/loading"),
								success:function(form,action) {
									if (action.result.data.type == "IMAGE") {
										$("#ModulePopupAddImageHelp").html("기존에 첨부된 [" + action.result.data.image.name + "] 이미지를 변경하시려면 변경할 파일을 선택하여 주십시오.");
									}
								},
								failure:function(form,action) {
									if (action.result && action.result.message) {
										Ext.Msg.show({title:Admin.getText("alert/error"),msg:action.result.message,buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
									} else {
										Ext.Msg.show({title:Admin.getText("alert/error"),msg:Admin.getErrorText("DATA_LOAD_FAILED"),buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
									}
									Ext.getCmp("ModulePopupAddWindow").close();
								}
							});
						}
					}
				}
			}).show();
		},
		delete:function() {
			var selected = Ext.getCmp("ModulePopupList").getSelectionModel().getSelection();
			if (selected.length == 0) {
				Ext.Msg.show({title:Admin.getText("alert/error"),msg:"삭제할 팝업을 선택하여 주십시오.",buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
				return;
			}
			
			var idxes = [];
			for (var i=0, loop=selected.length;i<loop;i++) {
				idxes.push(selected[i].get("idx"));
			}
			
			Ext.Msg.show({title:Admin.getText("alert/info"),msg:"선택한 팝업을 삭제하시겠습니까?",buttons:Ext.Msg.OKCANCEL,icon:Ext.Msg.QUESTION,fn:function(button) {
				if (button == "ok") {
					Ext.Msg.wait(Admin.getText("action/working"),Admin.getText("action/wait"));
					$.send(ENV.getProcessUrl("popup","@deletePopup"),{idx:idxes.join(",")},function(result) {
						if (result.success == true) {
							Ext.Msg.show({title:Admin.getText("alert/info"),msg:Admin.getText("action/worked"),buttons:Ext.Msg.OK,icon:Ext.Msg.INFO,fn:function() {
								Ext.getCmp("ModulePopupList").getStore().reload();
							}});
						}
					});
				}
			}});
		}
	},
	admin:{
		add:function(midx) {
			var midx = midx ? midx : 0;
			
			new Ext.Window({
				id:"ModulePopupAdminAddWindow",
				title:(midx ? "관리자 수정" : "관리자 추가"),
				width:600,
				height:500,
				modal:true,
				border:false,
				layout:"fit",
				items:[
					new Ext.Panel({
						border:false,
						layout:"fit",
						tbar:[
							new Ext.form.Hidden({
								id:"ModulePopupAdminAddMidx",
								name:"midx",
								value:midx,
								disabled:midx
							}),
							new Ext.form.TextField({
								id:"ModulePopupAdminAddText",
								name:"text",
								emptyText:"검색버튼을 클릭하여 관리자로 지정할 회원을 검색하세요.",
								readOnly:true,
								flex:1,
								listeners:{
									focus:function() {
										Member.search(function(member) {
											var text = member.name + "(" + member.nickname + ") / " + member.email;
											Ext.getCmp("ModulePopupAdminAddText").setValue(text);
											Ext.getCmp("ModulePopupAdminAddMidx").setValue(member.idx);
										});
									}
								}
							}),
							new Ext.Button({
								iconCls:"mi mi-search",
								text:"검색",
								disabled:midx,
								handler:function() {
									Member.search(function(member) {
										var text = member.name + "(" + member.nickname + ") / " + member.email;
										Ext.getCmp("ModulePopupAdminAddText").setValue(text);
										Ext.getCmp("ModulePopupAdminAddMidx").setValue(member.idx);
									});
								}
							}),
							"-",
							new Ext.form.Checkbox({
								id:"ModulePopupAdminAddAll",
								boxLabel:"전체사이트 관리",
								listeners:{
									change:function(form,value) {
										Ext.getCmp("ModulePopupAdminAddList").setDisabled(value);
									}
								}
							})
						],
						items:[
							new Ext.grid.Panel({
								id:"ModulePopupAdminAddList",
								border:false,
								selected:[],
								layout:"fit",
								autoScroll:true,
								store:new Ext.data.JsonStore({
									proxy:{
										type:"ajax",
										simpleSortMode:true,
										url:ENV.getProcessUrl("admin","@getSites"),
										extraParams:{depth:"group",parent:"NONE"},
										reader:{type:"json"}
									},
									remoteSort:false,
									sorters:[{property:"title",direction:"ASC"}],
									autoLoad:false,
									pageSize:0,
									fields:["domain","title"],
									listeners:{
										load:function(store,records,success,e) {
											if (success == true) {
												Ext.getCmp("ModulePopupAdminAddList").getSelectionModel().deselectAll(true);
												var selected = Ext.getCmp("ModulePopupAdminAddList").selected;
												for (var i=0, loop=store.getCount();i<loop;i++) {
													if ($.inArray(store.getAt(i).get("domain"),selected) > -1) {
														Ext.getCmp("ModulePopupAdminAddList").getSelectionModel().select(i,true);
													}
												}
											} else {
												if (e.getError()) {
													Ext.Msg.show({title:Admin.getText("alert/error"),msg:e.getError(),buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
												} else {
													Ext.Msg.show({title:Admin.getText("alert/error"),msg:Admin.getErrorText("LOAD_DATA_FAILED"),buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
												}
											}
										}
									}
								}),
								columns:[{
									text:"도메인",
									width:180,
									dataIndex:"domain",
								},{
									text:"사이트명",
									flex:1,
									dataIndex:"title"
								}],
								selModel:new Ext.selection.CheckboxModel({mode:"SIMPLE"})
							})
						]
					})
				],
				buttons:[
					new Ext.Button({
						text:(midx ? "권한수정하기" : "관리자 추가하기"),
						handler:function() {
							var midx = Ext.getCmp("ModulePopupAdminAddMidx").getValue();
							if (Ext.getCmp("ModulePopupAdminAddAll").getValue() == true) {
								var domain = "*";
							} else {
								var domains = Ext.getCmp("ModulePopupAdminAddList").getSelectionModel().getSelection();
								for (var i=0, loop=domains.length;i<loop;i++) {
									domains[i] = domains[i].get("domain");
								}
								var domain = domains.join(",");
							}
							
							if (!midx) {
								Ext.Msg.show({title:Admin.getText("alert/error"),msg:"관리자로 추가할 회원을 검색하여 주십시오.",buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
							} else {
								Ext.Msg.wait(Admin.getText("action/working"),Admin.getText("action/saving"));
								$.send(ENV.getProcessUrl("popup","@saveAdmin"),{midx:midx,domain:domain},function(result) {
									if (result.success == true) {
										Ext.Msg.show({title:Admin.getText("alert/info"),msg:Admin.getText("action/saved"),buttons:Ext.Msg.OK,icon:Ext.Msg.INFO,fn:function() {
											Ext.getCmp("ModulePopupAdminList").getStore().reload();
											Ext.getCmp("ModulePopupAdminAddWindow").close();
										}});
									}
								});
							}
						}
					}),
					new Ext.Button({
						text:"닫기",
						handler:function() {
							Ext.getCmp("ModulePopupAdminAddWindow").close();
						}
					})
				],
				listeners:{
					show:function() {
						if (midx == 0) {
							Ext.getCmp("ModulePopupAdminAddList").getStore().load();
						} else {
							Ext.Msg.wait(Admin.getText("action/working"),Admin.getText("action/loading"));
							$.send(ENV.getProcessUrl("popup","@getAdmin"),{midx:midx},function(result) {
								if (result.success == true) {
									Ext.Msg.hide();
									Ext.getCmp("ModulePopupAdminAddText").setValue(result.member.name+"("+result.member.nickname+") / "+result.member.email);
									if (result.domain == "*") {
										Ext.getCmp("ModulePopupAdminAddAll").setValue(true);
									} else {
										Ext.getCmp("ModulePopupAdminAddAll").setValue(false);
										Ext.getCmp("ModulePopupAdminAddList").selected = result.domain;
									}
									Ext.getCmp("ModulePopupAdminAddList").getStore().load();;
								}
							});
						}
					}
				}
			}).show();
		},
		/**
		 * 관리자 삭제
		 */
		delete:function(midx) {
			Ext.Msg.show({title:Admin.getText("alert/info"),msg:"관리자를 삭제하시겠습니까?",buttons:Ext.Msg.OKCANCEL,icon:Ext.Msg.QUESTION,fn:function(button) {
				if (button == "ok") {
					Ext.Msg.wait(Admin.getText("action/working"),Admin.getText("action/loading"));
					$.send(ENV.getProcessUrl("popup","@deleteAdmin"),{midx:midx},function(result) {
						if (result.success == true) {
							Ext.Msg.show({title:Admin.getText("alert/info"),msg:Admin.getText("action/worked"),buttons:Ext.Msg.OK,icon:Ext.Msg.INFO,fn:function() {
								Ext.getCmp("ModulePopupAdminList").getStore().reload();
							}});
						}
					});
				}
			}});
		},
	}
};