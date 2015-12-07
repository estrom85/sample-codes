/*
 * File:   main.cpp
 * Author: mato
 *
 * Created on Nedeľa, 2013, december 8, 12:52
 */

#include <QtGui/QApplication>
#include "PackagerForm.h"

int main(int argc, char *argv[]) {
    // initialize resources, if needed
    // Q_INIT_RESOURCE(resfile);

    QApplication app(argc, argv);
    app.setApplicationName("PackageManager");
    PackagerForm form;
    form.show();
    

    // create and show your widgets here

    return app.exec();
}
