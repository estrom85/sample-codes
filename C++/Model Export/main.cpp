#include <QtGui/QApplication>
#include "mainwindow.h"


int main(int argc, char *argv[])
{
    QApplication a(argc, argv);

    MainWindow w;
    //if(argc!=0)
     //   w.loadFile(argv[0]);
    w.show();
    return a.exec();
}
