MOVEPATH = "F:\wamp\www\test\onve\1\"
DESPATH = "F:\wamp\www\test\onve\2\"

Function FilesTree(sPath,dPath)    
'遍历一个文件夹下的所有文件夹文件夹    
    Set oFso = CreateObject("Scripting.FileSystemObject")    
    Set oFolder = oFso.GetFolder(sPath)    
    Set oSubFolders = oFolder.SubFolders    
    '判断目录是否存在
    If oFso.folderExists(dPath) Then         
            
    Else 
        oFso.createfolder(dPath)
    End If
    Set oFiles = oFolder.Files  
    For Each oFile In oFiles    
        'WScript.Echo oFile.size
        '大于10M，移动
        If oFile.size > 10485760 Then
            'WScript.Echo oFile.Path
            oFso.CopyFile oFile.Path,(dPath&oFile.Name)
        End If   
    Next    
        
    For Each oSubFolder In oSubFolders 
        call FilesTree(oSubFolder.Path,dPath)'递归    
    Next    
        
    Set oFolder = Nothing    
    Set oSubFolders = Nothing    
    Set oFso = Nothing    
End Function    
    
call FilesTree(MOVEPATH,DESPATH) '遍历
WScript.Echo("由"&MOVEPATH&"大于10M的文件，复制到"&DESPATH&"成功")