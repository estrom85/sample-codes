#ifndef MOVEDIALOG_H
#define MOVEDIALOG_H

#include <QDialog>
#include <QPair>
#include "item.h"

namespace Ui {
class MoveDialog;
}

class MoveDialog : public QDialog
{
    Q_OBJECT

private:
    explicit MoveDialog(Item* root, Item* currentItem, QWidget *parent = 0);

    void addCategory(Item* category,Item* current, QString prefix);
    void setComponent();

public:
    ~MoveDialog();

    static void moveItem(Item* currentItem, Item* root,DBConnection &db, QString &label, QWidget *parent=0);
    
private slots:
    void on_comboBox_currentIndexChanged(int index);

private:
    Ui::MoveDialog *ui;

    Item* root;
    Item* newParent;
    QList<QPair<QString,Item*> > categories;

};

#endif // MOVEDIALOG_H
