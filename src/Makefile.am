bin_PROGRAMS = tint
tint_SOURCES = tint.cpp

# set the include path found by configure
AM_CPPFLAGS =  $(LIBSDL_CFLAGS) $(all_includes)

# the library search path.
tint_LDFLAGS = $(all_libraries) $(LIBSDL_RPATH)
tint_LDADD = $(LIBSDL_LIBS)
