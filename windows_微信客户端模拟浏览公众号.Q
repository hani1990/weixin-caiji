[General]
SyntaxVersion=2
BeginHotkey=121
BeginHotkeyMod=0
PauseHotkey=0
PauseHotkeyMod=0
StopHotkey=123
StopHotkeyMod=0
RunOnce=1
EnableWindow=
MacroID=4408a45f-7226-4998-9751-42f7c80faef8
Description=我的脚本1
Enable=1
AutoRun=0
[Repeat]
Type=0
Number=1
[SetupUI]
Type=2
QUI=
[Relative]
SetupOCXFile=
[Comment]

[Script]
delay_time = 1000

//公众号垂直循环次数
loop_mp = 3
//两个公众号的水平距离
deltaX = 80
//垂直方向偏移
deltaY = 100
//历史记录按钮距离公众号的偏移
histroyX = 200
histroyY = 160

//微信客户端初始化
Call WexinInit
Delay delay_time
//进入微信公众号
Call GoToMP


For 2//循环页数
//左起第一个公众号Y坐标
intY = 150
	For 4//垂直方向公众号个数
	//左起第一个公众号X坐标
	intX = 423
	
		For 3//循环点击水平方向的公众号
			
			Call ClickHistory(intX, intY)
		//	//微信浏览器
			Delay 2000
			//模拟浏览历史列表页
			Call ViewPage
			intX = intX + deltaX
		Next
	
	intY = intY + deltaY
	TracePrint intY
	Next
MoveTo 700, 490
LeftClick 1
Next

Function WexinInit()
		//找到微信窗体句柄
	Hwnd = Plugin.Window.Find("WeChatMainWndForPC", "微信")
	TracePrint Hwnd
	Call Plugin.Window.Active(Hwnd)
	
	Call Plugin.Window.Top(Hwnd, 0)   
	
	//缩小微信窗体
	Call Plugin.Window.Size(Hwnd, 100, 100) 
	//移动到左上角
	Call Plugin.Window.Move(Hwnd, 0, 0)
End Function

Function GoToMP()
	//点击公众号
	MoveTo 25, 140
	LeftClick 1
	
	MoveTo 120,220
	LeftClick 1
End Function

Function ClickHistory(intX, intY)
//点击微信公众号进入历史页面
	MoveTo intX, intY
	Delay 500
		LeftClick 1
		Delay delay_time
	//找历史消息记录的位置，x轴固定 intX+histroyX Y轴 循环寻找
    	deltahY = 0
		For 20
			deltahY = deltahY + 10
			TracePrint deltahY
			MoveTo intX + histroyX, intY + histroyY +deltahY
			Delay delay_time
			FindColor 200,200,1024,768,"19AD1A",intXH,intYH
			If intXH > 0 And intYH > 0 Then 
				LeftClick 1
				Exit For
			End If
					
		Next
End Function


Function ViewPage()
//模拟浏览历史列表页
	Hwnd_weixin = Plugin.Window.Find("CefWebViewWnd", "微信")
	Call Plugin.Window.Active(Hwnd_weixin)
	Call Plugin.Window.Top(Hwnd_weixin, 0)
	brower_x = 1000
	brower_y = 0
	Call Plugin.Window.Move(Hwnd_weixin, brower_x, brower_y)
	MoveTo brower_x+50, brower_y+100
	For 10
		MouseWheel - 10 
		Delay 500
	Next
End Function