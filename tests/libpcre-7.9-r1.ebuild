# Copyright 1999-2009 Gentoo Foundation
# Distributed under the terms of the GNU General Public License v2
# $Header: /var/cvsroot/gentoo-x86/dev-libs/libpcre/libpcre-7.9-r1.ebuild,v 1.12 2009/10/21 08:56:24 loki_val Exp $

EAPI=2

inherit libtool eutils toolchain-funcs autotools

DESCRIPTION="Perl-compatible regular expression library"
HOMEPAGE="http://www.pcre.org/"
if [[ ${PV} == ${PV/_rc} ]]
then
	MY_P="pcre-${PV}"
	SRC_URI="ftp://ftp.csx.cam.ac.uk/pub/software/programming/pcre/${MY_P}.tar.bz2"
else
	MY_P="pcre-${PV/_rc/-RC}"
	SRC_URI="ftp://ftp.csx.cam.ac.uk/pub/software/programming/pcre/Testing/${MY_P}.tar.bz2"
fi
LICENSE="BSD"
SLOT="3"
KEYWORDS="alpha amd64 arm hppa ia64 m68k ~mips ppc ppc64 s390 sh sparc ~sparc-fbsd x86 ~x86-fbsd"
IUSE="bzip2 +cxx doc unicode zlib static-libs"

RDEPEND="bzip2? ( app-arch/bzip2 )
	zlib? ( sys-libs/zlib )"
DEPEND="${RDEPEND}
	dev-util/pkgconfig
	userland_GNU? ( >=sys-apps/findutils-4.4.0 )"

S=${WORKDIR}/${MY_P}

src_prepare() {
	sed -i -e "s:libdir=@libdir@:libdir=/$(get_libdir):" libpcre.pc.in || die "Fixing libpcre pkgconfig files failed"
	sed -i -e "s:-lpcre ::" libpcrecpp.pc.in || die "Fixing libpcrecpp pkgconfig files failed"
	echo "Requires: libpcre = @PACKAGE_VERSION@" >> libpcrecpp.pc.in
	epatch "${FILESDIR}"/libpcre-7.9-pkg-config.patch
	eautoreconf
	elibtoolize
}

src_configure() {
	econf --with-match-limit-recursion=8192 \
		$(use_enable unicode utf8) $(use_enable unicode unicode-properties) \
		$(use_enable cxx cpp) \
		$(use_enable zlib pcregrep-libz) \
		$(use_enable bzip2 pcregrep-libbz2) \
		$(use_enable static-libs static) \
		--enable-shared \
		--htmldir=/usr/share/doc/${PF}/html \
		--docdir=/usr/share/doc/${PF} \
		--with-link-size=3 \
		|| die "econf failed"
}

src_install() {
	emake DESTDIR="${D}" install || die "make install failed"

	gen_usr_ldscript -a pcre

	dodoc doc/*.txt AUTHORS
	use doc && dohtml doc/html/*
	find "${D}" -type f -name '*.la' -exec rm -rf '{}' '+' || die "la removal failed"
}

pkg_postinst() {
	elog "This version of ${PN} has stopped installing .la files. This may"
	elog "cause compilation failures in other packages. To fix this problem,"
	elog "install dev-util/lafilefixer and run:"
	elog "lafilefixer --justfixit"
}
