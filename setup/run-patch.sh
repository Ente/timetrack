#!/bin/bash

# This little script applies the ldaptools-php8.patch onto the repo.
# Since PHP 8 certain LDAP paged functions have been removed. This patch applies the new constants to the desired PageControl class

dir=${PWD%/*}
cd $dir/vendor/ldaptools/ldaptools
patch -p1 < ../../../patch/ldaptools-php8.patch