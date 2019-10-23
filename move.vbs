MOVEPATH = "F:\wamp\www\test\onve\1\"
DESPATH = "F:\wamp\www\test\onve\2\"

Function FilesTree(sPath,dPath)    
'����һ���ļ����µ������ļ����ļ���    
    Set oFso = CreateObject("Scripting.FileSystemObject")    
    Set oFolder = oFso.GetFolder(sPath)    
    Set oSubFolders = oFolder.SubFolders    
    '�ж�Ŀ¼�Ƿ����
    If oFso.folderExists(dPath) Then         
            
    Else 
        oFso.createfolder(dPath)
    End If
    Set oFiles = oFolder.Files  
    For Each oFile In oFiles    
        'WScript.Echo oFile.size
        '����10M���ƶ�
        If oFile.size > 10485760 Then
            'WScript.Echo oFile.Path
            oFso.CopyFile oFile.Path,(dPath&oFile.Name)
        End If   
    Next    
        
    For Each oSubFolder In oSubFolders 
        call FilesTree(oSubFolder.Path,dPath)'�ݹ�    
    Next    
        
    Set oFolder = Nothing    
    Set oSubFolders = Nothing    
    Set oFso = Nothing    
End Function    
    
call FilesTree(MOVEPATH,DESPATH) '����
WScript.Echo("��"&MOVEPATH&"����10M���ļ������Ƶ�"&DESPATH&"�ɹ�")