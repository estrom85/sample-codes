#ifndef MAINWINDOW_H
#define MAINWINDOW_H

#include <QMainWindow>
#include <QString>
#include <string>

#include "COLLADA/CCollada.h"

namespace Ui {
    class MainWindow;
}

class MainWindow : public QMainWindow
{
    Q_OBJECT

public:
    explicit MainWindow(QWidget *parent = 0);
    ~MainWindow();

private slots:
    void on_actionKoniec_activated();

    void on_panelGL_destroyed();

    void on_pushButton_2_clicked();

    void on_pushButton_3_clicked();

private:
    Ui::MainWindow *ui;

    CCollada *collada;

signals:
    void importFile(CCollada* file, char* filename);
    void importFile(char* filename);
    void exportFile(char* filename);



public:
    void loadFile(const char* filename);
    void saveFile(const char* filename);
    void close();

};

#endif // MAINWINDOW_H
