USE [master]
GO
/****** Object:  Database [meta_zakupki]    Script Date: 07.02.2023 17:43:30 ******/
CREATE DATABASE [meta_zakupki]
 CONTAINMENT = NONE
 ON  PRIMARY 
( NAME = N'meta_zakupki', FILENAME = N'G:\zakupki\meta_zakupki.mdf' , SIZE = 113940352KB , MAXSIZE = UNLIMITED, FILEGROWTH = 1024KB )
 LOG ON 
( NAME = N'meta_zakupki_log', FILENAME = N'G:\zakupki\meta_zakupki_log.ldf' , SIZE = 6166080KB , MAXSIZE = 2048GB , FILEGROWTH = 10%)
GO
ALTER DATABASE [meta_zakupki] SET COMPATIBILITY_LEVEL = 110
GO
IF (1 = FULLTEXTSERVICEPROPERTY('IsFullTextInstalled'))
begin
EXEC [meta_zakupki].[dbo].[sp_fulltext_database] @action = 'enable'
end
GO
ALTER DATABASE [meta_zakupki] SET ANSI_NULL_DEFAULT OFF 
GO
ALTER DATABASE [meta_zakupki] SET ANSI_NULLS OFF 
GO
ALTER DATABASE [meta_zakupki] SET ANSI_PADDING OFF 
GO
ALTER DATABASE [meta_zakupki] SET ANSI_WARNINGS OFF 
GO
ALTER DATABASE [meta_zakupki] SET ARITHABORT OFF 
GO
ALTER DATABASE [meta_zakupki] SET AUTO_CLOSE OFF 
GO
ALTER DATABASE [meta_zakupki] SET AUTO_CREATE_STATISTICS ON 
GO
ALTER DATABASE [meta_zakupki] SET AUTO_SHRINK OFF 
GO
ALTER DATABASE [meta_zakupki] SET AUTO_UPDATE_STATISTICS ON 
GO
ALTER DATABASE [meta_zakupki] SET CURSOR_CLOSE_ON_COMMIT OFF 
GO
ALTER DATABASE [meta_zakupki] SET CURSOR_DEFAULT  GLOBAL 
GO
ALTER DATABASE [meta_zakupki] SET CONCAT_NULL_YIELDS_NULL OFF 
GO
ALTER DATABASE [meta_zakupki] SET NUMERIC_ROUNDABORT OFF 
GO
ALTER DATABASE [meta_zakupki] SET QUOTED_IDENTIFIER OFF 
GO
ALTER DATABASE [meta_zakupki] SET RECURSIVE_TRIGGERS OFF 
GO
ALTER DATABASE [meta_zakupki] SET  DISABLE_BROKER 
GO
ALTER DATABASE [meta_zakupki] SET AUTO_UPDATE_STATISTICS_ASYNC OFF 
GO
ALTER DATABASE [meta_zakupki] SET DATE_CORRELATION_OPTIMIZATION OFF 
GO
ALTER DATABASE [meta_zakupki] SET TRUSTWORTHY OFF 
GO
ALTER DATABASE [meta_zakupki] SET ALLOW_SNAPSHOT_ISOLATION OFF 
GO
ALTER DATABASE [meta_zakupki] SET PARAMETERIZATION SIMPLE 
GO
ALTER DATABASE [meta_zakupki] SET READ_COMMITTED_SNAPSHOT OFF 
GO
ALTER DATABASE [meta_zakupki] SET HONOR_BROKER_PRIORITY OFF 
GO
ALTER DATABASE [meta_zakupki] SET RECOVERY SIMPLE 
GO
ALTER DATABASE [meta_zakupki] SET  MULTI_USER 
GO
ALTER DATABASE [meta_zakupki] SET PAGE_VERIFY CHECKSUM  
GO
ALTER DATABASE [meta_zakupki] SET DB_CHAINING OFF 
GO
ALTER DATABASE [meta_zakupki] SET FILESTREAM( NON_TRANSACTED_ACCESS = OFF ) 
GO
ALTER DATABASE [meta_zakupki] SET TARGET_RECOVERY_TIME = 0 SECONDS 
GO
EXEC sys.sp_db_vardecimal_storage_format N'meta_zakupki', N'ON'
GO
USE [meta_zakupki]
GO
/****** Object:  StoredProcedure [dbo].[addDatafile]    Script Date: 07.02.2023 17:43:30 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE PROCEDURE [dbo].[addDatafile]
	@pd char(32),
	@pn varchar(32),
	@fn nvarchar(200),
	@fd nvarchar(200),
	@dat varchar(50)
AS
DECLARE @e nvarchar(20), @p int, @n int,@t char(20),@dd datetime
BEGIN
	--SET NOCOUNT ON;
    if not exists (select 1 from Datafiles where contentID=@pd)
    BEGIN 
	   set @dd=convert(datetimeOffset,@dat,120)
	   set @e=SUBSTRING((LOWER(REVERSE(SUBSTRING(REVERSE(@fn),0,CHARINDEX('.',REVERSE(@fn),IIF(CHARINDEX('.part',@fn)>0,1+CHARINDEX('.',REVERSE(@fn)),0)))))),1,20)
	   set @n=(select coeff from meta_zakupki.dbo.Exts WHERE ext=@e)
	   set @t=(select [type] from zakupki_work.dbo.purchasesLt p where p.purchasenumber=@pn and oid is not null)
	   if(@n is null or @t is null)begin
	    set @p=null
	   end else begin
	    if(@t like 'EP%')
		BEGIN
		 set @p=IIF(@n<0,-99,1)
		END ELSE BEGIN
	     if(@n<0)
		 BEGIN
		  set @p=(YEAR(@dat)-YEAR(GETDATE()))*12+MONTH(@dd)-15+IIF(@fn LIKE '%смета%' OR @fn LIKE '%ТЗ%' OR @fn LIKE '%техническ%' OR @fn LIKE '%задание%' OR @fn LIKE '%объект%' OR @fn LIKE '%описание%' OR @fn LIKE '%требован%' OR @fn LIKE '%заявк%' OR @fn LIKE '%изменен%' OR @fn LIKE '%прил%' OR @fn LIKE '%разд%',1,0)
		 END ELSE BEGIN
	      set @p=(YEAR(@dat)-2014)*12+MONTH(@dd)+IIF(@fn LIKE '%смета%' OR @fn LIKE '%ТЗ%' OR @fn LIKE '%техническ%' OR @fn LIKE '%задание%' OR @fn LIKE '%объект%' OR @fn LIKE '%описание%' OR @fn LIKE '%требован%' OR @fn LIKE '%заявк%' OR @fn LIKE '%изменен%' OR @fn LIKE '%прил%' OR @fn LIKE '%разд%',3,0)
		 END
        END
	   end
	   insert into DataFiles (purchasenumber,contentID,[filename],filedescription,filedate,clientid,ext,[priority])  
       values (@pn,@pd,@fn,@fd,@dd,0,@e,@p)
    END
END

GO
/****** Object:  StoredProcedure [dbo].[addLoadedFile]    Script Date: 07.02.2023 17:43:30 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO


-- Batch submitted through debugger: SQLQuery36.sql|7|0|C:\Users\ser\AppData\Local\Temp\2\~vsFA81.sql
-- =============================================
-- Author:		<Author,,Name>
-- Create date: <Create Date,,>
-- Description:	<Description,,>
-- =============================================
CREATE PROCEDURE [dbo].[addLoadedFile] 
	-- Add the parameters for the stored procedure here
	@fn varchar(100),
	@mode int,
	@datatype int
	 
	
	AS
	declare @tid int;
 
BEGIN
	SET NOCOUNT ON;
	 select @tid=(select  top 1 id FROM [LoadedFiles] WHERE [filename]=@fn and [datatype]=@datatype);
	if (@tid>0)and(@mode=1)
begin 
	  update [LoadedFiles] set loaded=1, dateload=getdate() WHERE id=@tid;
end 
else 
 if (@tid>0) begin return @tid; end
       else 
	    begin
	      insert into [LoadedFiles] (filename,datatype) values (@fn,@datatype);
  	      SELECT @tid = 0;
	    end;
	 return @tid;
    END



GO
/****** Object:  StoredProcedure [dbo].[addMetaFile]    Script Date: 07.02.2023 17:43:30 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE PROCEDURE [dbo].[addMetaFile]
	@fileid CHAR(32),
	@filename NVARCHAR(200),
	@XML NVARCHAR(MAX)
 AS
 DECLARE
    @fid INT,
	@idoc INT
BEGIN
	SET NOCOUNT ON;
	SET @filename=replace(@filename,'\','/')
	SELECT @fid=id FROM Metafiles where contentID=@fileid AND [filename]=@filename
	IF(@fid IS NULL)BEGIN
	 INSERT INTO Metafiles(contentID,[filename]) VALUES(@fileid,@filename)
	 SET @fid=@@IDENTITY
    END ELSE BEGIN
     DELETE FROM Metatags WHERE [file]=@fid
	END
    EXEC sp_xml_preparedocument @idoc OUTPUT, @XML;
	INSERT INTO Metatags([file],tag,value,code)
    SELECT @fid,XTag,Value,(SELECT TOP 1 Code FROM meta_zakupki..TagsWhitelist w WHERE w.Tag=UPPER(XTag)) FROM OPENXML(@idoc,'/root/tag',1)
        WITH (XTag nvarchar(200) '@t',Value nvarchar(200) '@v')
		WHERE XTag in (SELECT Tag FROM meta_zakupki..TagsWhitelist);
    merge LineTags with(HOLDLOCK) as t
    using (select f.id,a=(select top 1 value from Metatags where [file]=f.id and (tag='Creator' or tag='Author')),m=(select value from Metatags where [file]=f.id and tag='LastModifiedBy'),c=(select value from Metatags where [file]=f.id and tag='Company') from Metafiles f WHERE f.id=@fid)
    as source (id, a, m, c)
    on t.fileid = source.id
when matched then
    update
    set creator = source.a,
        modifier = source.m,
		company = source.c
when not matched then
    insert (fileid, creator, modifier, company)
    values ( source.id, source.a, source.m, source.c );

END

GO
/****** Object:  StoredProcedure [dbo].[CreateWorkDatabase]    Script Date: 07.02.2023 17:43:30 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

CREATE PROCEDURE [dbo].[CreateWorkDatabase] as
	
BEGIN
	--SET NOCOUNT ON;
truncate table [meta_work].dbo.DataFiles;
ALTER INDEX ALL ON [meta_work].dbo.Datafiles  DISABLE; 
insert into  [meta_work].dbo.Datafiles  (fileid,purchaseNumber,contentid,[filename],filedate) 
 select metafiles.id,b.purchasenumber,metafiles.contentid,metafiles.[filename],b.filedate
	from Metafiles inner join datafiles as b on (metafiles.contentid=b.contentid) where
upper(metafiles.[filename]) like '%СМЕТА%'
or metafiles.filename like '%ТЗ%' 
or metafiles.filename like  '%ТЕХНИЧЕСК%'
or metafiles.filename like  '%ЗАДАНИЕ%'
or metafiles.filename like  '%ОБЪЕКТ%'
or metafiles.filename like  '%ОПИСАН%'
or metafiles.filename like  '%ТРЕБОВАН%'
or metafiles.filename like  '%ЗАЯВ%'
or metafiles.filename like  '%ИЗМЕНЕН%'
or metafiles.filename like  '%ПРИЛ%'
or metafiles.filename like  '%РАЗД%';
--31964000
ALTER INDEX ALL ON [meta_work].dbo.Datafiles  ReBuild;
truncate table meta_work.dbo.FileIndex;
ALTER INDEX ALL ON [meta_work].dbo.FileIndex  DISABLE;  
insert into meta_work.dbo.fileIndex (fileid,coid,oid) 
 select datafiles.Fileid,a.oid,a.coid from meta_work.dbo.datafiles 
  inner join zakupki_work.dbo.purchasesLt as a on a.purchaseNumber=meta_work.dbo.datafiles.purchasenumber;
ALTER INDEX ALL ON [meta_work].dbo.FileIndex  ReBuild;
truncate table meta_work.dbo.MetaTags;
ALTER INDEX ALL ON [meta_work].dbo.MetaTags  DISABLE;  
insert into meta_work.dbo.Metatags (fileid,tag,value) select
    [file],tag,value from metatags where (tag in ('Creator','Company','LastModifiedBy','Author','Subject','Initial-creator','LastAuthor'))
				and
			([file] in ( select fileId from meta_work.dbo.fileIndex));

ALTER INDEX ALL ON [meta_work].dbo.MetaTags  Rebuild;  
END


GO
/****** Object:  StoredProcedure [dbo].[getFiles]    Script Date: 07.02.2023 17:43:30 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE PROCEDURE [dbo].[getFiles] 
	@id INT
	AS
	DECLARE @Filter TABLE (ID VARCHAR(22),Dummy INT);
	DECLARE @Tmp TABLE (ID VARCHAR(22),Dummy INT);
BEGIN
   SET NOCOUNT ON;
   -- status 0 - ok, -1 == prepare, -2 == sent, >0 == error code
--   SET @cn=(SELECT count(*) FROM Datafiles WHERE clientID=@id AND ([status]<0 OR [status] IS NULL))
   IF(EXISTS(SELECT 1 FROM Datafiles WHERE clientID=@id AND ([status]<0 OR [status] IS NULL)))BEGIN
    UPDATE Datafiles SET [status]=NULL,[priority]=[priority]-1,clientID=0 WHERE clientID=@id AND [status]<-5
    UPDATE Datafiles SET [status]=[status]-1,[time]=GETDATE() WHERE clientID=@id AND [status]<0
	SELECT ContentID FROM Datafiles WHERE clientID=@id AND [status]<0 
	UNION 
	SELECT ContentID FROM Datafiles WHERE clientID=@id AND [status] IS NULL
	UNION 
	SELECT ContentID FROM Datafiles WHERE clientID=@id AND [status]>0 AND [time]<DATEADD(HOUR,-3,GETDATE())
	RETURN
   END
   BEGIN TRY
     -- Записываем в список 10 закупок, у которых есть какие-то нужные необработанные файлы (сначала с наибольшим приоритетом)
	 insert into @Tmp  select top 999 [purchasenumber],[priority] FROM Datafiles WHERE  [priority]>=0 and clientid=0  ORDER BY [priority] DESC
     --INSERT INTO @Filter SELECT DISTINCT TOP 99 purchasenumber,[priority] FROM Datafiles WHERE clientID=0 AND [priority]>=0 ORDER BY [priority] DESC
	 IF(EXISTS(SELECT 1 FROM @Tmp))
	 BEGIN
       insert into @Filter select distinct top 96 Id,Dummy from @tmp ORDER BY Dummy DESC
       BEGIN TRAN T1
	   -- Только угодные нам расширения, список можно пополнять (многотомные учитывать потом отдельным запросом)
	   UPDATE Datafiles SET clientID=@id,[status]=-1 WHERE purchasenumber IN (SELECT ID FROM @Filter)  AND [priority]>=0 AND [status] IS NULL
       COMMIT TRAN T1
--       INSERT INTO UserLogs (UserID,Action,IP) VALUES(@id,-1,'T1 ok')
	 END
   END TRY
   BEGIN CATCH
     ROLLBACK TRAN T1
	 SELECT TOP 0 ContentID FROM Datafiles
	 --INSERT INTO UserLogs (UserID,Action,IP) VALUES(@id,-1,'T1 error')
	 RETURN
   END CATCH
--   INSERT INTO @Res
   SELECT ContentID FROM Datafiles WHERE clientID=@id AND [status]<0
   BEGIN TRY
	BEGIN TRAN T2
	UPDATE Datafiles SET clientID=@id,[status]=-2,[time]=GETDATE() WHERE clientID=@id AND [status]=-1
	COMMIT TRAN T2
    --INSERT INTO UserLogs (UserID,Action,IP) VALUES(@id,-1,'T2 ok')
   END TRY
   BEGIN CATCH
    ROLLBACK TRAN T2
    --INSERT INTO UserLogs (UserID,Action,IP) VALUES(@id,-1,'T2 error')
   END CATCH
--   SELECT ID FROM @Res
   RETURN
END
GO
/****** Object:  StoredProcedure [dbo].[getXFiles]    Script Date: 07.02.2023 17:43:30 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE PROCEDURE [dbo].[getXFiles] 
	@id INT
	AS
	DECLARE @Filter TABLE (ID VARCHAR(22),Dummy INT);
	DECLARE @Tmp TABLE (ID VARCHAR(22),Dummy INT);
BEGIN
   SET NOCOUNT ON;
   -- status 0 - ok, -1 == prepare, -2 == sent, >0 == error code
--   SET @cn=(SELECT count(*) FROM Datafiles WHERE clientID=@id AND ([status]<0 OR [status] IS NULL))
   IF(EXISTS(SELECT 1 FROM Datafiles WHERE clientID=@id AND ([status]<0 OR [status] IS NULL)))BEGIN
	 -- ВРЕМЕНО!!!
    IF(@id<>62 AND @id<>63)BEGIN
    UPDATE Datafiles SET [status]=NULL,[priority]=[priority]-7,clientID=0 WHERE clientID=@id AND [status]<-3
    UPDATE Datafiles SET [status]=[status]-1,[time]=GETDATE() WHERE clientID=@id AND [status]<0
	 -- ВРЕМЕНО!!!
    END
	SELECT TOP 22 ContentID FROM Datafiles WHERE clientID=@id AND [status]<0 
	UNION 
	SELECT ContentID FROM Datafiles WHERE clientID=@id AND [status] IS NULL
    UNION 
    SELECT ContentID FROM Datafiles WHERE clientID=@id AND [status]>0 AND [status]<>416 AND [time]<DATEADD(HOUR,-3,GETDATE())
	UNION 
	SELECT ContentID FROM Datafiles WHERE clientID=@id AND [status]=416 AND [time]<DATEADD(HOUR,-1,GETDATE())
	RETURN
   END
   BEGIN TRY
     -- Записываем в список 10 закупок, у которых есть какие-то нужные необработанные файлы (сначала с наибольшим приоритетом)
	 insert into @Tmp select top 999 [purchasenumber],[priority] FROM Datafiles WHERE clientid=0 and [priority] is not null and priority<0
	 ORDER BY [priority] desc
	 IF((SELECT count(*) FROM @Tmp)<22)BEGIN
       insert into @Tmp select top 999 [purchasenumber],[priority] FROM Datafiles WHERE clientid=0 and [priority] is not null and priority>=0
       ORDER BY [priority] desc
	 END
	 IF(EXISTS(SELECT 1 FROM @Tmp))
	 BEGIN
       insert into @Filter select distinct top 42 Id,Dummy from @tmp order by Dummy desc;
       BEGIN TRAN T1
	   -- Только угодные нам расширения, список можно пополнять (многотомные учитывать потом отдельным запросом)
	   UPDATE Datafiles SET clientID=@id,[status]=-1 WHERE purchasenumber IN (SELECT ID FROM @Filter) AND [priority] IS NOT NULL AND [status] IS NULL --                       AND ([priority]<0 OR ([priority]>0 AND (filename like '%.zip')))
	   print @@rowcount
       COMMIT TRAN T1
--       INSERT INTO UserLogs (UserID,Action,IP) VALUES(@id,-1,'T1 ok')
	 END
   END TRY
   BEGIN CATCH
     ROLLBACK TRAN T1
	 print 'err1'
	 SELECT TOP 0 ContentID FROM Datafiles
	 --INSERT INTO UserLogs (UserID,Action,IP) VALUES(@id,-1,'T1 error')
	 RETURN
   END CATCH
--   INSERT INTO @Res
   SELECT ContentID FROM Datafiles WHERE clientID=@id AND [status]<0
   UNION 
   SELECT ContentID FROM Datafiles WHERE clientID=@id AND [status]>0 AND [status]<>416 AND [time]<DATEADD(HOUR,-3,GETDATE())
   UNION 
   SELECT ContentID FROM Datafiles WHERE clientID=@id AND [status]=416 AND [time]<DATEADD(HOUR,-1,GETDATE())
   BEGIN TRY
	BEGIN TRAN T2
	UPDATE Datafiles SET clientID=@id,[status]=-2,[time]=GETDATE() WHERE clientID=@id AND [status]=-1
	COMMIT TRAN T2
    --INSERT INTO UserLogs (UserID,Action,IP) VALUES(@id,-1,'T2 ok')
   END TRY
   BEGIN CATCH
	 print 'err2'
    ROLLBACK TRAN T2
    --INSERT INTO UserLogs (UserID,Action,IP) VALUES(@id,-1,'T2 error')
   END CATCH
--   SELECT ID FROM @Res
   RETURN
END
GO
/****** Object:  StoredProcedure [dbo].[MetasToLine]    Script Date: 07.02.2023 17:43:30 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE PROCEDURE [dbo].[MetasToLine]
AS
BEGIN
	SET NOCOUNT ON;

    merge LineTags with(HOLDLOCK) as t
    using (select f.id,a=(select value from Metatags where [file]=f.id and (tag='Creator' or tag='Author')),m=(select value from Metatags where [file]=f.id and tag='LastModifiedBy'),c=(select value from Metatags where [file]=f.id and tag='Company') from Metafiles f)
    as source (id, a, m, c)
    on t.fileid = source.id
when matched then
    update
    set creator = source.a,
        modifier = source.m,
		company = source.c
when not matched then
    insert (fileid, creator, modifier, company)
    values ( source.id, source.a, source.m, source.c );
END

GO
/****** Object:  StoredProcedure [dbo].[updateFileInfo]    Script Date: 07.02.2023 17:43:30 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE PROCEDURE [dbo].[updateFileInfo]
	@fileid CHAR(32),
	@status INT,
	@user INT
AS
BEGIN
	SET NOCOUNT ON;
	-- проверка что не перезаписываем кривым статусом нулдевой
	UPDATE Datafiles SET [status]=@status,[time]=GETDATE(),clientid=@user WHERE contentID=@fileid AND (@status=0 OR [status] IS NULL OR [status]<>0)
	SELECT @@ROWCOUNT
END

GO
/****** Object:  StoredProcedure [dbo].[updatePriority]    Script Date: 07.02.2023 17:43:30 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE PROCEDURE [dbo].[updatePriority]
AS
DECLARE @year INT,@xdate datetime
BEGIN
	--SET NOCOUNT ON;
	-- Сбрасываем приоритет у файлов, которые не должны быть обработаны (но только у необработанных)
	--UPDATE Datafiles SET [priority]=NULL WHERE [priority] IS NOT NULL AND clientID=0 AND (LOWER(REVERSE(SUBSTRING(REVERSE(filename),0,CHARINDEX('.',REVERSE(filename))))) NOT IN (SELECT ext FROM meta_zakupki..Exts))
	UPDATE Datafiles SET [status]=NULL,clientID=0 WHERE [status]>=400 AND [priority] IS NOT NULL AND [time]<DATEADD(HOUR,-1,GETDATE())
	-- 0 - is a priority for bads (temporary) UPDATE Datafiles SET clientID=0,[priority]=-1 WHERE [status] IS NULL AND [priority]=0
	-- Убираем подвисшие данные
	UPDATE Datafiles SET clientID=0,[time]=NULL,[status]=NULL WHERE [priority] IS NOT NULL AND [status]<0 AND [time] IS NOT NULL AND DATEDIFF(HOUR,[time],GETDATE())>7

CREATE TABLE #tmp(p VARCHAR(32),c CHAR(32))
-- дата, глубже которой не копаем
SET @xdate=cast('2018-1-1' AS DATETIME)
SET @year=YEAR(GETDATE())
INSERT INTO #tmp
SELECT purchasenumber,contentid FROM Datafiles d WITH(NOLOCK)
WHERE clientid=0 AND d.[priority] IS NULL AND ext IN(SELECT ext FROM Exts) AND filedate>@xdate
UPDATE datafiles SET [priority]=IIF((SELECT coeff FROM Exts e WHERE e.ext=datafiles.ext)>0,
            (YEAR(filedate)-2014)*12+MONTH(filedate),
            (YEAR(filedate)-@year)*12+MONTH(filedate)-14)
WHERE [priority] IS NULL AND contentid IN(SELECT c FROM #tmp WHERE p IN(SELECT purchasenumber FROM zakupki_work.dbo.purchasesLt WHERE oid IS NOT NULL))
DROP TABLE #tmp
END

GO
/****** Object:  StoredProcedure [dbo].[UpdateTagCodes]    Script Date: 07.02.2023 17:43:30 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE PROCEDURE [dbo].[UpdateTagCodes]
AS
BEGIN
  UPDATE Metatags SET code=(SELECT TOP 1 Code FROM TagsWhitelist w WHERE w.Tag=UPPER(Metatags.Tag))
END

GO
/****** Object:  Table [dbo].[Datafiles]    Script Date: 07.02.2023 17:43:30 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[Datafiles](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[purchasenumber] [varchar](22) NULL,
	[contentID] [char](32) NULL,
	[filename] [nvarchar](200) NULL,
	[status] [int] NULL,
	[clientID] [int] NULL,
	[time] [datetimeoffset](7) NULL,
	[priority] [int] NULL,
	[filedescription] [nvarchar](200) NULL,
	[filedate] [datetimeoffset](7) NULL,
	[ext] [nvarchar](20) NULL
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[Exts]    Script Date: 07.02.2023 17:43:30 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[Exts](
	[ext] [nvarchar](10) NOT NULL,
	[coeff] [int] NULL
) ON [PRIMARY]

GO
/****** Object:  Table [dbo].[Linetags]    Script Date: 07.02.2023 17:43:30 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[Linetags](
	[fileid] [int] NOT NULL,
	[creator] [nvarchar](200) NULL,
	[modifier] [nvarchar](200) NULL,
	[company] [nvarchar](200) NULL
) ON [PRIMARY]

GO
/****** Object:  Table [dbo].[LoadedFiles]    Script Date: 07.02.2023 17:43:30 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[LoadedFiles](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[filename] [varchar](100) NULL,
	[dateload] [datetime] NULL,
	[loaded] [smallint] NULL,
	[datatype] [int] NULL
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[Metafiles]    Script Date: 07.02.2023 17:43:30 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[Metafiles](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[contentID] [char](32) NOT NULL,
	[filename] [nvarchar](200) NOT NULL,
 CONSTRAINT [PK_metafiles] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[Metatags]    Script Date: 07.02.2023 17:43:30 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[Metatags](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[file] [int] NOT NULL,
	[tag] [nvarchar](64) NOT NULL,
	[value] [nvarchar](200) NULL,
	[code] [int] NULL,
 CONSTRAINT [PK_Metatags] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
/****** Object:  Table [dbo].[TagsWhitelist]    Script Date: 07.02.2023 17:43:30 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[TagsWhitelist](
	[Tag] [nvarchar](64) NOT NULL,
	[Code] [int] NULL,
 CONSTRAINT [PK_TagsWhitelist] PRIMARY KEY CLUSTERED 
(
	[Tag] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
/****** Object:  Table [dbo].[Users]    Script Date: 07.02.2023 17:43:30 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[Users](
	[ID] [int] IDENTITY(1,1) NOT NULL,
	[UID] [uniqueidentifier] NOT NULL,
	[LastActive] [datetime] NOT NULL CONSTRAINT [DF_Users_LastActive]  DEFAULT (getdate()),
 CONSTRAINT [PK_Users] PRIMARY KEY CLUSTERED 
(
	[ID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING ON

GO
/****** Object:  Index [name]    Script Date: 07.02.2023 17:43:30 ******/
CREATE CLUSTERED INDEX [name] ON [dbo].[LoadedFiles]
(
	[filename] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
SET ANSI_PADDING ON

GO
/****** Object:  Index [By_contentId]    Script Date: 07.02.2023 17:43:30 ******/
CREATE NONCLUSTERED INDEX [By_contentId] ON [dbo].[Datafiles]
(
	[contentID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
SET ANSI_PADDING ON

GO
/****** Object:  Index [By_purchaseid]    Script Date: 07.02.2023 17:43:30 ******/
CREATE NONCLUSTERED INDEX [By_purchaseid] ON [dbo].[Datafiles]
(
	[purchasenumber] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
/****** Object:  Index [cid_priority]    Script Date: 07.02.2023 17:43:30 ******/
CREATE NONCLUSTERED INDEX [cid_priority] ON [dbo].[Datafiles]
(
	[clientID] ASC,
	[priority] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
SET ANSI_PADDING ON

GO
/****** Object:  Index [Clientid_priority_purchase]    Script Date: 07.02.2023 17:43:30 ******/
CREATE NONCLUSTERED INDEX [Clientid_priority_purchase] ON [dbo].[Datafiles]
(
	[priority] DESC,
	[clientID] ASC
)
INCLUDE ( 	[purchasenumber]) WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
/****** Object:  Index [Datafiles_status]    Script Date: 07.02.2023 17:43:30 ******/
CREATE NONCLUSTERED INDEX [Datafiles_status] ON [dbo].[Datafiles]
(
	[clientID] ASC,
	[status] ASC
)
INCLUDE ( 	[priority]) WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
SET ANSI_PADDING ON

GO
/****** Object:  Index [Priority_ext]    Script Date: 07.02.2023 17:43:30 ******/
CREATE NONCLUSTERED INDEX [Priority_ext] ON [dbo].[Datafiles]
(
	[priority] ASC,
	[ext] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
/****** Object:  Index [By_fileid]    Script Date: 07.02.2023 17:43:30 ******/
CREATE NONCLUSTERED INDEX [By_fileid] ON [dbo].[Linetags]
(
	[fileid] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
SET ANSI_PADDING ON

GO
/****** Object:  Index [By_contentid]    Script Date: 07.02.2023 17:43:30 ******/
CREATE NONCLUSTERED INDEX [By_contentid] ON [dbo].[Metafiles]
(
	[contentID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
SET ANSI_PADDING ON

GO
/****** Object:  Index [NonClusteredIndex-20220924-231632]    Script Date: 07.02.2023 17:43:30 ******/
CREATE NONCLUSTERED INDEX [NonClusteredIndex-20220924-231632] ON [dbo].[Metatags]
(
	[file] ASC,
	[tag] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
ALTER TABLE [dbo].[Metatags]  WITH CHECK ADD  CONSTRAINT [FK_Metatags_metafiles] FOREIGN KEY([file])
REFERENCES [dbo].[Metafiles] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[Metatags] CHECK CONSTRAINT [FK_Metatags_metafiles]
GO
USE [master]
GO
ALTER DATABASE [meta_zakupki] SET  READ_WRITE 
GO
