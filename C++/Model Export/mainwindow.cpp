#include "mainwindow.h"
#include "ui_mainwindow.h"
#include <QFileDialog>
#include <cctype>



MainWindow::MainWindow(QWidget *parent) :
    QMainWindow(parent),
    ui(new Ui::MainWindow)
{
    ui->setupUi(this);
    collada=0;
}

MainWindow::~MainWindow()
{
    delete ui;
    delete collada;
}

void MainWindow::on_actionKoniec_activated()
{
    this->close();
}



void MainWindow::on_pushButton_2_clicked()
{
    QString path=QFileDialog::getOpenFileName(this,tr("Importuj"),"../",
                                              tr("COLLADA files(*.dae);; MDG files(*.mdg)"));
    std::string colFile=path.toStdString();

    if(!path.isNull())
    {

    loadFile(colFile.c_str());
    }

}

void MainWindow::on_pushButton_3_clicked()
{
    QString path=QFileDialog::getSaveFileName(this,tr("Exportuj"),"../model.mdg",tr("MDG files(*.mdg)"));

    std::string file=path.toStdString();

    if(!path.isNull())
        saveFile(file.c_str());

}

void MainWindow::saveFile(const char *filename)
{
    emit exportFile(const_cast<char*>(filename));
}

void MainWindow::loadFile(const char *filename)
{
    std::string temp=filename;
    int beg=temp.find_last_of(".")+1;
    int end=temp.length();

    std::string filetype=temp.substr(beg,end);

    for(int i=0;i<filetype.length();i++)
    {
        filetype[i]=std::tolower(filetype[i]);
    }

    if(filetype=="dae")
    {
            if(collada!=0) delete collada;
            collada = new CCollada(filename);
            emit importFile(collada, const_cast<char*>(filename));
    }
    else if(filetype=="mdg")
    {
            emit importFile(const_cast<char*>(filename));

    }


}

void MainWindow::close()
{
    QMainWindow::close();
}


