Set fso = CreateObject("Scripting.FileSystemObject")
Set WshShell = CreateObject("WScript.Shell")

currentDir = fso.GetParentFolderName(WScript.ScriptFullName)
botDir = currentDir & "\whatsapp-bot"

nodeBin = "node"
If fso.FileExists("D:\nodeJS\node.exe") Then
    nodeBin = "D:\nodeJS\node.exe"
ElseIf fso.FileExists("C:\Program Files\nodejs\node.exe") Then
    nodeBin = "C:\Program Files\nodejs\node.exe"
ElseIf fso.FileExists("C:\laragon\bin\nodejs\node.exe") Then
    nodeBin = "C:\laragon\bin\nodejs\node.exe"
ElseIf fso.FileExists("D:\laragon\bin\nodejs\node.exe") Then
    nodeBin = "D:\laragon\bin\nodejs\node.exe"
End If

WshShell.CurrentDirectory = botDir
WshShell.Run """" & nodeBin & """ server.js", 0, False
