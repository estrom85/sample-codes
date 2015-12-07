#-------------------------------------------------
#
# Project created by QtCreator 2013-02-12T16:10:11
#
#-------------------------------------------------

QT       += core gui sql

TARGET = Kucharka
TEMPLATE = app


SOURCES += main.cpp\
        mainwindow.cpp \
    dbconnection.cpp \
    item.cpp \
    receiptdescription.cpp \
    queryresult.cpp \
    categorytree.cpp \
    categorytreeeditable.cpp \
    movedialog.cpp

HEADERS  += mainwindow.h \
    dbconnection.h \
    item.h \
    receiptdescription.h \
    queryresult.h \
    categorytree.h \
    categorytreeeditable.h \
    movedialog.h

FORMS    += mainwindow.ui \
    movedialog.ui
