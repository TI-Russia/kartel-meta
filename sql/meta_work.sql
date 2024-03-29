USE [master]
GO
/****** Object:  Database [meta_work]    Script Date: 07.02.2023 17:45:14 ******/
CREATE DATABASE [meta_work]
 CONTAINMENT = NONE
 ON  PRIMARY 
( NAME = N'meta_work', FILENAME = N'F:\Database\meta_work.mdf' , SIZE = 12484800KB , MAXSIZE = UNLIMITED, FILEGROWTH = 1024KB )
 LOG ON 
( NAME = N'meta_work_log', FILENAME = N'F:\Database\meta_work_log.ldf' , SIZE = 15993536KB , MAXSIZE = 2048GB , FILEGROWTH = 10%)
GO
ALTER DATABASE [meta_work] SET COMPATIBILITY_LEVEL = 110
GO
IF (1 = FULLTEXTSERVICEPROPERTY('IsFullTextInstalled'))
begin
EXEC [meta_work].[dbo].[sp_fulltext_database] @action = 'enable'
end
GO
ALTER DATABASE [meta_work] SET ANSI_NULL_DEFAULT OFF 
GO
ALTER DATABASE [meta_work] SET ANSI_NULLS OFF 
GO
ALTER DATABASE [meta_work] SET ANSI_PADDING OFF 
GO
ALTER DATABASE [meta_work] SET ANSI_WARNINGS OFF 
GO
ALTER DATABASE [meta_work] SET ARITHABORT OFF 
GO
ALTER DATABASE [meta_work] SET AUTO_CLOSE OFF 
GO
ALTER DATABASE [meta_work] SET AUTO_CREATE_STATISTICS ON 
GO
ALTER DATABASE [meta_work] SET AUTO_SHRINK OFF 
GO
ALTER DATABASE [meta_work] SET AUTO_UPDATE_STATISTICS ON 
GO
ALTER DATABASE [meta_work] SET CURSOR_CLOSE_ON_COMMIT OFF 
GO
ALTER DATABASE [meta_work] SET CURSOR_DEFAULT  GLOBAL 
GO
ALTER DATABASE [meta_work] SET CONCAT_NULL_YIELDS_NULL OFF 
GO
ALTER DATABASE [meta_work] SET NUMERIC_ROUNDABORT OFF 
GO
ALTER DATABASE [meta_work] SET QUOTED_IDENTIFIER OFF 
GO
ALTER DATABASE [meta_work] SET RECURSIVE_TRIGGERS OFF 
GO
ALTER DATABASE [meta_work] SET  DISABLE_BROKER 
GO
ALTER DATABASE [meta_work] SET AUTO_UPDATE_STATISTICS_ASYNC OFF 
GO
ALTER DATABASE [meta_work] SET DATE_CORRELATION_OPTIMIZATION OFF 
GO
ALTER DATABASE [meta_work] SET TRUSTWORTHY OFF 
GO
ALTER DATABASE [meta_work] SET ALLOW_SNAPSHOT_ISOLATION OFF 
GO
ALTER DATABASE [meta_work] SET PARAMETERIZATION SIMPLE 
GO
ALTER DATABASE [meta_work] SET READ_COMMITTED_SNAPSHOT OFF 
GO
ALTER DATABASE [meta_work] SET HONOR_BROKER_PRIORITY OFF 
GO
ALTER DATABASE [meta_work] SET RECOVERY BULK_LOGGED 
GO
ALTER DATABASE [meta_work] SET  MULTI_USER 
GO
ALTER DATABASE [meta_work] SET PAGE_VERIFY CHECKSUM  
GO
ALTER DATABASE [meta_work] SET DB_CHAINING OFF 
GO
ALTER DATABASE [meta_work] SET FILESTREAM( NON_TRANSACTED_ACCESS = OFF ) 
GO
ALTER DATABASE [meta_work] SET TARGET_RECOVERY_TIME = 0 SECONDS 
GO
EXEC sys.sp_db_vardecimal_storage_format N'meta_work', N'ON'
GO
USE [meta_work]
GO
/****** Object:  StoredProcedure [dbo].[CreateByCompany]    Script Date: 07.02.2023 17:45:14 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
-- =============================================
-- Author:		<Author,,Name>
-- Create date: <Create Date,,>
-- Description:	<Description,,>
-- =============================================
CREATE PROCEDURE [dbo].[CreateByCompany] 
	as
BEGIN
	-- SET NOCOUNT ON added to prevent extra result sets from
	-- interfering with SELECT statements.
	SET NOCOUNT ON;
truncate table ByCompany;
insert into Bycompany (name,cnt,foid,value,tag) 
select * from
(select orgs.name,a.cnt,a.foid,a.value,a.tag from
(select count(*) as cnt,fileindex.coid as foid,value,tag from fileindex inner join metatags on metatags.fileid=fileindex.fileid 
	group by fileindex.coid,value,tag) as a
	inner join zakupki.dbo.orgs on orgs.oid=a.foid
	 --where len(a.value)>2 and a.cnt>1 
	 ) as b 
	 --where b.name like '%'+b.value+'%' order by b.value ;
	 truncate table uniqueTags;
insert into UniqueTags (name,cnt,foid,value,tag) 
select c.name,c.cnt,c.foid,b.value,b.tag from
(select count(*) as cn,value,tag from
(SELECT [name]
      ,[foid]
      ,[cnt]
      ,[value],[tag]
  FROM [meta_work].[dbo].[ByCompany] where len(value)>3) as a group by a.value,a.tag) as b
  inner join meta_work.dbo.Bycompany as c on b.value=c.value and b.tag=c.tag where cn=1 and cnt>4
  order by b.value;

  truncate table ByCompany;

insert into Bycompany (name,cnt,foid,value,tag) 
select * from
(select orgs.name,a.cnt,a.foid,a.value,a.tag from
(select count(*) as cnt,fileindex.oid as foid,value,tag from fileindex inner join metatags on metatags.fileid=fileindex.fileid 
	group by fileindex.oid,value,tag) as a
	inner join zakupki.dbo.orgs on orgs.oid=a.foid
	 --where len(a.value)>2 and a.cnt>1 
	 ) as b 
 truncate table uniqueTags_z;
insert into UniqueTags_z (name,cnt,foid,value,tag) 
select c.name,c.cnt,c.foid,b.value,b.tag from
(select count(*) as cn,value,tag from
(SELECT [name]
      ,[foid]
      ,[cnt]
      ,[value],[tag]
  FROM [meta_work].[dbo].[ByCompany] where len(value)>3) as a group by a.value,a.tag) as b
  inner join meta_work.dbo.Bycompany as c on b.value=c.value and b.tag=c.tag where cn=1 and cnt>4
  order by b.value;
  truncate table multiTags_z;
  insert into MultiTags_z (name,cnt,foid,value,tag) 
select c.name,c.cnt,c.foid,b.value,b.tag from
(select count(*) as cn,value,tag from
(SELECT [name]
      ,[foid]
      ,[cnt]
      ,[value],[tag]
  FROM [meta_work].[dbo].[ByCompany] where len(value)>3) as a group by a.value,a.tag) as b
  inner join meta_work.dbo.Bycompany as c on b.value=c.value and b.tag=c.tag where cn>3 and cnt>4
  order by b.value;
END

GO
/****** Object:  StoredProcedure [dbo].[CreateByUser]    Script Date: 07.02.2023 17:45:15 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

-- =============================================
-- Author:		<Author,,Name>
-- Create date: <Create Date,,>
-- Description:	<Description,,>
-- =============================================
CREATE PROCEDURE [dbo].[CreateByUser] 
	as
BEGIN
	-- SET NOCOUNT ON added to prevent extra result sets from
	-- interfering with SELECT statements.
	SET NOCOUNT ON;
truncate table ByUser;
insert into ByUser (name,cnt,foid,value,MetaTag) 
select *,'Author' from
(select orgs.name,a.cnt,a.foid,a.value from
(select count(*) as cnt,fileindex.coid as foid,value from fileindex inner join metatags on metatags.fileid=fileindex.fileid 
	where tag='Author' group by fileindex.coid,value) as a
	inner join zakupki.dbo.orgs on orgs.oid=a.foid where len(a.value)>2 and a.cnt>1 ) as b where b.name like '%'+b.value+'%' order by b.value ;

insert into ByUser (name,cnt,foid,value,MetaTag) 
select *,'Creator' from
(select orgs.name,a.cnt,a.foid,a.value from
(select count(*) as cnt,fileindex.coid as foid,value from fileindex inner join metatags on metatags.fileid=fileindex.fileid 
	where tag='Creator' group by fileindex.coid,value) as a
	inner join zakupki.dbo.orgs on orgs.oid=a.foid where len(a.value)>2 and a.cnt>2 ) as b 
	where b.name like '%'+b.value+'%' 
	order by b.value ;

insert into ByUser (name,cnt,foid,value,MetaTag) 
select *,'LastModifiedBy' from
(select orgs.name,a.cnt,a.foid,a.value from
(select count(*) as cnt,fileindex.coid as foid,value from fileindex inner join metatags on metatags.fileid=fileindex.fileid 
	where tag='LastModifiedBy' group by fileindex.coid,value) as a
	inner join zakupki.dbo.orgs on orgs.oid=a.foid where len(a.value)>2 and a.cnt>2 ) as b 
	where b.name like '%'+b.value+'%' 
	order by b.value ;
insert into ByUser (name,cnt,foid,value,MetaTag) 
select *,'Company' from
(select orgs.name,a.cnt,a.foid,a.value from
(select count(*) as cnt,fileindex.coid as foid,value from fileindex inner join metatags on metatags.fileid=fileindex.fileid 
	where tag='Company' group by fileindex.coid,value) as a
	inner join zakupki.dbo.orgs on orgs.oid=a.foid where len(a.value)>3 and a.cnt>2 ) as b 
	where b.name like '%'+dbo.metatag_cut(b.value)+'%' 
	order by b.value ;

delete from ByUser where value like '%????%';
delete from ByUser where value ='___';
delete from ByUser where value ='____';
END


GO
/****** Object:  UserDefinedFunction [dbo].[metatag_cut]    Script Date: 07.02.2023 17:45:15 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE FUNCTION [dbo].[metatag_cut]
(@data varchar (100))
RETURNS varchar(100)
BEGIN 
-- return @data;
   RETURN ltrim(replace(replace(@data,'"',''),'ООО',''));

END

GO
/****** Object:  Table [dbo].[ByCompany]    Script Date: 07.02.2023 17:45:15 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[ByCompany](
	[name] [varchar](200) NULL,
	[foid] [int] NULL,
	[cnt] [int] NULL,
	[value] [varchar](200) NULL,
	[tag] [varchar](50) NULL
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[ByUser]    Script Date: 07.02.2023 17:45:15 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[ByUser](
	[name] [varchar](200) NULL,
	[foid] [int] NULL,
	[cnt] [int] NULL,
	[value] [varchar](200) NULL,
	[MetaTag] [varchar](30) NULL
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[Datafiles]    Script Date: 07.02.2023 17:45:15 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[Datafiles](
	[FileId] [int] NOT NULL,
	[purchasenumber] [varchar](22) NULL,
	[contentID] [char](32) NULL,
	[FileName] [varchar](200) NULL,
	[filedate] [datetimeoffset](7) NULL
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[FIleIndex]    Script Date: 07.02.2023 17:45:15 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[FIleIndex](
	[FileId] [int] NOT NULL,
	[coid] [int] NULL,
	[oid] [int] NULL
) ON [PRIMARY]

GO
/****** Object:  Table [dbo].[Metatags]    Script Date: 07.02.2023 17:45:15 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[Metatags](
	[fileId] [int] NOT NULL,
	[tag] [nvarchar](18) NOT NULL,
	[value] [nvarchar](200) NULL,
	[code] [int] NULL
) ON [PRIMARY]

GO
/****** Object:  Table [dbo].[MultiTags_Z]    Script Date: 07.02.2023 17:45:15 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[MultiTags_Z](
	[name] [varchar](200) NULL,
	[foid] [int] NULL,
	[cnt] [int] NULL,
	[value] [varchar](200) NULL,
	[tag] [varchar](50) NULL
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[UniqueTags]    Script Date: 07.02.2023 17:45:15 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[UniqueTags](
	[name] [varchar](200) NULL,
	[foid] [int] NULL,
	[cnt] [int] NULL,
	[value] [varchar](200) NULL,
	[tag] [varchar](50) NULL
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[UniqueTags_Z]    Script Date: 07.02.2023 17:45:15 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[UniqueTags_Z](
	[name] [varchar](200) NULL,
	[foid] [int] NULL,
	[cnt] [int] NULL,
	[value] [varchar](200) NULL,
	[tag] [varchar](50) NULL
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  UserDefinedFunction [dbo].[ft_GetPageUsers]    Script Date: 07.02.2023 17:45:15 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

-- =============================================
-- Author:		<Author,,Name>
-- Create date: <Create Date,,>
-- Description:	<Description,,>
-- =============================================
CREATE FUNCTION [dbo].[ft_GetPageUsers](@Page INT, --Номер страницы
                                  @CntRowOnPage AS INT  --Количество записей на странице
								
                                  ) 
   RETURNS TABLE
   RETURN(
   --Объявляем CTE
   WITH SOURCE AS (
        SELECT ROW_NUMBER() OVER (ORDER BY foId) AS RowNumber, * 
        FROM ByUser 
   )
   SELECT * FROM SOURCE
   WHERE RowNumber > (@Page * @CntRowOnPage) - @CntRowOnPage 
     AND RowNumber <= @Page * @CntRowOnPage
  )


GO
/****** Object:  UserDefinedFunction [dbo].[ft_GetPageUsers_cnt]    Script Date: 07.02.2023 17:45:15 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

-- =============================================
-- Author:		<Author,,Name>
-- Create date: <Create Date,,>
-- Description:	<Description,,>
-- =============================================
CREATE FUNCTION [dbo].[ft_GetPageUsers_cnt](@Page INT, --Номер страницы
                                  @CntRowOnPage AS INT  --Количество записей на странице
								
                                  ) 
   RETURNS TABLE
   RETURN(
   --Объявляем CTE
   WITH SOURCE AS (
        SELECT ROW_NUMBER() OVER (ORDER BY cnt desc) AS RowNumber, * 
        FROM ByUser 
   )
   SELECT * FROM SOURCE
   WHERE RowNumber > (@Page * @CntRowOnPage) - @CntRowOnPage 
     AND RowNumber <= @Page * @CntRowOnPage
  )


GO
/****** Object:  UserDefinedFunction [dbo].[ft_GetPageUsers_value]    Script Date: 07.02.2023 17:45:15 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

-- =============================================
-- Author:		<Author,,Name>
-- Create date: <Create Date,,>
-- Description:	<Description,,>
-- =============================================
create FUNCTION [dbo].[ft_GetPageUsers_value](@Page INT, --Номер страницы
                                  @CntRowOnPage AS INT  --Количество записей на странице
								
                                  ) 
   RETURNS TABLE
   RETURN(
   --Объявляем CTE
   WITH SOURCE AS (
        SELECT ROW_NUMBER() OVER (ORDER BY value desc) AS RowNumber, * 
        FROM ByUser 
   )
   SELECT * FROM SOURCE
   WHERE RowNumber > (@Page * @CntRowOnPage) - @CntRowOnPage 
     AND RowNumber <= @Page * @CntRowOnPage
  )


GO
/****** Object:  Index [By_id]    Script Date: 07.02.2023 17:45:15 ******/
CREATE NONCLUSTERED INDEX [By_id] ON [dbo].[Datafiles]
(
	[FileId] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
SET ANSI_PADDING ON

GO
/****** Object:  Index [By_Purchase]    Script Date: 07.02.2023 17:45:15 ******/
CREATE NONCLUSTERED INDEX [By_Purchase] ON [dbo].[Datafiles]
(
	[purchasenumber] ASC
)
INCLUDE ( 	[FileId]) WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
/****** Object:  Index [By_fileId]    Script Date: 07.02.2023 17:45:15 ******/
CREATE NONCLUSTERED INDEX [By_fileId] ON [dbo].[FIleIndex]
(
	[FileId] ASC
)
INCLUDE ( 	[coid]) WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
/****** Object:  Index [By_id]    Script Date: 07.02.2023 17:45:15 ******/
CREATE NONCLUSTERED INDEX [By_id] ON [dbo].[Metatags]
(
	[fileId] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
SET ANSI_PADDING ON

GO
/****** Object:  Index [By_value]    Script Date: 07.02.2023 17:45:15 ******/
CREATE NONCLUSTERED INDEX [By_value] ON [dbo].[Metatags]
(
	[value] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
USE [master]
GO
ALTER DATABASE [meta_work] SET  READ_WRITE 
GO
