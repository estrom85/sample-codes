#ifndef MAINWINDOW_H
#define MAINWINDOW_H

#include <QMainWindow>
#include "categorytree.h"
#include "categorytreeeditable.h"
#include <QPicture>
#include <QPainter>
#include <QRect>
#include <QFileDialog>
#include <QMessageBox>
#include <QPrinter>
#include <QPrintDialog>

#include "movedialog.h"

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
    void on_actionViewMode_triggered();

    void on_actionEditMode_triggered();

    void on_actionExit_triggered();

    void on_actionAddCategory_triggered();

    void on_actionAddSubCategory_triggered();

    void on_actionRemoveItem_triggered();

    void on_actionAddReceipt_triggered();

    void on_data_Changed(const QModelIndex & topLeft, const QModelIndex & bottomRight);
/*
    void on_treeViewEdit_activated(const QModelIndex &index);

    void on_treeView_activated(const QModelIndex &index);
*/
    void on_edit_mode_ingredients_textChanged();

    void on_edit_mode_content_textChanged();

    void on_pushButton_clicked();

    void on_edit_mode_choose_picture_clicked();

    void on_actionSaveReceipt_triggered();

    void on_actionPrint_triggered();

    void on_treeView_clicked(const QModelIndex &index);

    void on_treeViewEdit_clicked(const QModelIndex &index);

    void on_actionMoveItem_triggered();

private:
    void init();
    void setReceipt();
    void displayRecipe();
    void setView(QAbstractItemModel* model);
    void setReceipt(const QModelIndex &index);
    void displayInfoMessage(QString &text, QString infotext=QString());
    void displayWarning(QString &text, QString infotext=QString());
    bool displayConfirmMessage(QString &text, QString &infotext);

private:
    Ui::MainWindow *ui;

    DBConnection db;
    Item *root;
    QAbstractItemModel *tree;
    RecipeDescription *currentReceipt;

};

#endif // MAINWINDOW_H
