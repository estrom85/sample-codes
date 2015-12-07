#-------------------------------------------------
#
# Project created by QtCreator 2011-12-28T09:39:22
#
#-------------------------------------------------

QT       += core gui opengl
TARGET = model_export
TEMPLATE = app


SOURCES += main.cpp\
        mainwindow.cpp \
    openglpanel.cpp \
    COLLADA/CSource.cpp \
    COLLADA/CMaterialLibrary.cpp \
    COLLADA/CImageLibrary.cpp \
    COLLADA/CGeometryLibrary.cpp \
    COLLADA/CCollada.cpp \
    pugixml.cpp \
    COLLADA/CEffectLibrary.cpp \
    MDG/CMDG.cpp \
    MDG/VertexData.cpp \
    MDG/FaceData.cpp \
    MDG/MaterialData.cpp \
    CVec3f.cpp \
    SOIL/stb_image_aug.c \
    SOIL/SOIL.c \
    SOIL/image_helper.c \
    SOIL/image_DXT.c \
    cquaternion.cpp

HEADERS  += mainwindow.h \
    openglpanel.h \
    COLLADA/CSource.h \
    COLLADA/CMaterialLibrary.h \
    COLLADA/CImageLibrary.h \
    COLLADA/CGeometryLibrary.h \
    COLLADA/CCollada.h \
    pugixml.hpp \
    pugiconfig.hpp \
    COLLADA/CEffectLibrary.h \
    MDG/CMDG.h \
    CVec3f.h \
    SOIL/stbi_DDS_aug_c.h \
    SOIL/stbi_DDS_aug.h \
    SOIL/stb_image_aug.h \
    SOIL/SOIL.h \
    SOIL/image_helper.h \
    SOIL/image_DXT.h \
    cquaternion.h

FORMS    += mainwindow.ui
























