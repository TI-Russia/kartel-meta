USE [master]
GO
/****** Object:  Database [zakupki_work]    Script Date: 07.02.2023 17:46:11 ******/
CREATE DATABASE [zakupki_work]
 CONTAINMENT = NONE
 ON  PRIMARY 
( NAME = N'zakupki_work', FILENAME = N'F:\Database\zakupki_work.mdf' , SIZE = 19728384KB , MAXSIZE = UNLIMITED, FILEGROWTH = 1024KB )
 LOG ON 
( NAME = N'zakupki_work_log', FILENAME = N'F:\Database\zakupki_work_log.ldf' , SIZE = 15993536KB , MAXSIZE = 2048GB , FILEGROWTH = 10%)
GO
ALTER DATABASE [zakupki_work] SET COMPATIBILITY_LEVEL = 110
GO
IF (1 = FULLTEXTSERVICEPROPERTY('IsFullTextInstalled'))
begin
EXEC [zakupki_work].[dbo].[sp_fulltext_database] @action = 'enable'
end
GO
ALTER DATABASE [zakupki_work] SET ANSI_NULL_DEFAULT OFF 
GO
ALTER DATABASE [zakupki_work] SET ANSI_NULLS OFF 
GO
ALTER DATABASE [zakupki_work] SET ANSI_PADDING OFF 
GO
ALTER DATABASE [zakupki_work] SET ANSI_WARNINGS OFF 
GO
ALTER DATABASE [zakupki_work] SET ARITHABORT OFF 
GO
ALTER DATABASE [zakupki_work] SET AUTO_CLOSE OFF 
GO
ALTER DATABASE [zakupki_work] SET AUTO_CREATE_STATISTICS ON 
GO
ALTER DATABASE [zakupki_work] SET AUTO_SHRINK OFF 
GO
ALTER DATABASE [zakupki_work] SET AUTO_UPDATE_STATISTICS ON 
GO
ALTER DATABASE [zakupki_work] SET CURSOR_CLOSE_ON_COMMIT OFF 
GO
ALTER DATABASE [zakupki_work] SET CURSOR_DEFAULT  GLOBAL 
GO
ALTER DATABASE [zakupki_work] SET CONCAT_NULL_YIELDS_NULL OFF 
GO
ALTER DATABASE [zakupki_work] SET NUMERIC_ROUNDABORT OFF 
GO
ALTER DATABASE [zakupki_work] SET QUOTED_IDENTIFIER OFF 
GO
ALTER DATABASE [zakupki_work] SET RECURSIVE_TRIGGERS OFF 
GO
ALTER DATABASE [zakupki_work] SET  DISABLE_BROKER 
GO
ALTER DATABASE [zakupki_work] SET AUTO_UPDATE_STATISTICS_ASYNC OFF 
GO
ALTER DATABASE [zakupki_work] SET DATE_CORRELATION_OPTIMIZATION OFF 
GO
ALTER DATABASE [zakupki_work] SET TRUSTWORTHY OFF 
GO
ALTER DATABASE [zakupki_work] SET ALLOW_SNAPSHOT_ISOLATION OFF 
GO
ALTER DATABASE [zakupki_work] SET PARAMETERIZATION SIMPLE 
GO
ALTER DATABASE [zakupki_work] SET READ_COMMITTED_SNAPSHOT OFF 
GO
ALTER DATABASE [zakupki_work] SET HONOR_BROKER_PRIORITY OFF 
GO
ALTER DATABASE [zakupki_work] SET RECOVERY FULL 
GO
ALTER DATABASE [zakupki_work] SET  MULTI_USER 
GO
ALTER DATABASE [zakupki_work] SET PAGE_VERIFY CHECKSUM  
GO
ALTER DATABASE [zakupki_work] SET DB_CHAINING OFF 
GO
ALTER DATABASE [zakupki_work] SET FILESTREAM( NON_TRANSACTED_ACCESS = OFF ) 
GO
ALTER DATABASE [zakupki_work] SET TARGET_RECOVERY_TIME = 0 SECONDS 
GO
EXEC sys.sp_db_vardecimal_storage_format N'zakupki_work', N'ON'
GO
USE [zakupki_work]
GO
/****** Object:  Table [dbo].[contractsLT]    Script Date: 07.02.2023 17:46:11 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[contractsLT](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[oid] [int] NULL,
	[contractnumber] [varchar](22) NULL,
	[contactphone] [varchar](20) NULL,
	[contactemail] [varchar](100) NULL,
	[purchasenumber] [varchar](22) NULL,
	[contractid] [varchar](22) NULL,
	[ver] [int] NULL,
	[price] [float] NULL,
	[date] [date] NULL,
	[coid] [int] NULL,
	[reg] [int] NULL,
	[lot] [int] NULL
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[purchasesLT]    Script Date: 07.02.2023 17:46:11 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[purchasesLT](
	[purchasenumber] [char](22) NULL,
	[coid] [int] NULL,
	[maxprice] [float] NULL,
	[date] [date] NULL,
	[status] [int] NULL,
	[cphone] [varchar](20) NULL,
	[cemail] [varchar](50) NULL,
	[lot] [int] NULL,
	[discount] [float] NULL,
	[okpd] [varchar](20) NULL,
	[type] [varchar](20) NULL,
	[oid] [int] NULL,
	[name] [varchar](400) NULL,
	[offers] [int] NULL,
	[rejected] [int] NULL,
	[budgetlvl] [int] NULL,
	[OKTMO] [varchar](12) NULL
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
SET ANSI_PADDING ON

GO
/****** Object:  Index [By_email]    Script Date: 07.02.2023 17:46:11 ******/
CREATE NONCLUSTERED INDEX [By_email] ON [dbo].[contractsLT]
(
	[contactemail] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
SET ANSI_PADDING ON

GO
/****** Object:  Index [ByphoneIndex]    Script Date: 07.02.2023 17:46:11 ******/
CREATE NONCLUSTERED INDEX [ByphoneIndex] ON [dbo].[contractsLT]
(
	[contactphone] ASC
)
INCLUDE ( 	[oid],
	[contractnumber],
	[contactemail],
	[coid]) WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
SET ANSI_PADDING ON

GO
/****** Object:  Index [Contracts_contractnumber]    Script Date: 07.02.2023 17:46:11 ******/
CREATE NONCLUSTERED INDEX [Contracts_contractnumber] ON [dbo].[contractsLT]
(
	[contractnumber] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
SET ANSI_PADDING ON

GO
/****** Object:  Index [ContractsLT.byPurchases]    Script Date: 07.02.2023 17:46:11 ******/
CREATE NONCLUSTERED INDEX [ContractsLT.byPurchases] ON [dbo].[contractsLT]
(
	[purchasenumber] ASC
)
INCLUDE ( 	[price],
	[oid]) WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
/****** Object:  Index [ContractsLT_oid]    Script Date: 07.02.2023 17:46:11 ******/
CREATE NONCLUSTERED INDEX [ContractsLT_oid] ON [dbo].[contractsLT]
(
	[oid] ASC
)
INCLUDE ( 	[reg]) WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
/****** Object:  Index [by_date]    Script Date: 07.02.2023 17:46:11 ******/
CREATE NONCLUSTERED INDEX [by_date] ON [dbo].[purchasesLT]
(
	[date] ASC
)
INCLUDE ( 	[maxprice]) WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
ALTER INDEX [by_date] ON [dbo].[purchasesLT] DISABLE
GO
/****** Object:  Index [By_oid]    Script Date: 07.02.2023 17:46:11 ******/
CREATE NONCLUSTERED INDEX [By_oid] ON [dbo].[purchasesLT]
(
	[oid] ASC
)
INCLUDE ( 	[coid]) WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
ALTER INDEX [By_oid] ON [dbo].[purchasesLT] DISABLE
GO
SET ANSI_PADDING ON

GO
/****** Object:  Index [PurchasesLT.byPurchase]    Script Date: 07.02.2023 17:46:11 ******/
CREATE NONCLUSTERED INDEX [PurchasesLT.byPurchase] ON [dbo].[purchasesLT]
(
	[purchasenumber] ASC
)
INCLUDE ( 	[lot],
	[maxprice]) WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
USE [master]
GO
ALTER DATABASE [zakupki_work] SET  READ_WRITE 
GO
