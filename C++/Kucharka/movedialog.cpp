#include "movedialog.h"
#include "ui_movedialog.h"

MoveDialog::MoveDialog(Item *root, Item *currentItem,QWidget *parent) :
    QDialog(parent),
    ui(new Ui::MoveDialog)
{
    ui->setupUi(this);
    this->root=root;
    addCategory(root,currentItem,QString());
    newParent=0;
    setComponent();

}

void MoveDialog::addCategory(Item *category, Item* current, QString prefix)
{
    if(!category->isCategory()) return;

    if(category!=root&&category!=current){
        QString label=prefix+category->getName();
        categories.append(QPair<QString,Item*>(label,category));
    }

    QList<Item*>::iterator i;
    QList<Item*> list=category->getList();
    for(i=list.begin();i!=list.end();++i){
        addCategory(*i,current,prefix+QString("-"));
    }
}

void MoveDialog::setComponent()
{
    QList<QPair<QString,Item*> >::iterator i;
    QPair<QString,Item*> item;
    for(i=categories.begin();i!=categories.end();++i){
        item=*i;
        ui->comboBox->addItem(item.first);
    }
}

MoveDialog::~MoveDialog()
{
    delete ui;
}

void MoveDialog::moveItem(Item *currentItem, Item *root, DBConnection &db, QString &label, QWidget *parent)
{
    if(currentItem==0||root==0) return;
    MoveDialog dialog(root,currentItem,parent);
    dialog.setWindowTitle(label);
    if(dialog.exec()==QDialog::Rejected) return;
    currentItem->moveItem(db,dialog.newParent);
    /*
    QMessageBox msg;
    msg.setText(currentItem->getName());
    msg.exec();
    */
}



void MoveDialog::on_comboBox_currentIndexChanged(int index)
{
    newParent=categories.value(index).second;
}
