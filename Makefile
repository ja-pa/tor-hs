#
# Copyright (C) 02020 CZ.NIC, z. s. p. o. (https://www.nic.cz/)
#
# This is free software, licensed under the GNU General Public License v2.
# See /LICENSE for more information.
#

include $(TOPDIR)/rules.mk

PKG_NAME:=tor-hs
PKG_VERSION:=0.0.1
PKG_RELEASE:=1

PKG_BUILD_DIR:=$(BUILD_DIR)/$(PKG_NAME)

PKG_MAINTAINER:=Jan Pavlinec <jan.pavlinec@nic.cz>
PKG_LICENSE:=GPL-3.0

PKG_INSTALL:=0

include $(INCLUDE_DIR)/package.mk

define Package/tor-hs
	SECTION:=net
	CATEGORY:=Network
	SUBMENU:=IP Addresses and Names
	TITLE:=Tor hidden service configurator
	DEPENDS:=+tor
endef

define Package/tor-hs/description
Tor Hidden Service configurator
endef

define Package/tor-hs/conffiles
/etc/config/tor-hs
endef

define Build/Compile
endef

define Build/Install
endef

define Package/tor-hs/install
	$(INSTALL_DIR) $(1)/etc/config/
	$(CP) ./files/tor-hs.conf $(1)/etc/config/tor-hs
	$(INSTALL_DIR) $(1)/etc/init.d/
	$(INSTALL_BIN) ./files/tor-hs.init $(1)/etc/init.d/tor-hs
	$(INSTALL_DIR) $(1)/etc/tor/
	$(INSTALL_BIN) ./files/nextcloud-update.php $(1)/etc/tor/
endef

$(eval $(call BuildPackage,tor-hs))
