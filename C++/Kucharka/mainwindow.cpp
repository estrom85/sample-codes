#include "mainwindow.h"
#include "ui_mainwindow.h"
#include <QDebug>

MainWindow::MainWindow(QWidget *parent) :
    QMainWindow(parent),
    ui(new Ui::MainWindow)
{
    ui->setupUi(this);

    init();
}

MainWindow::~MainWindow()
{
    delete ui;
    delete root;
    delete tree;

}

/*
  private methods
  */


void MainWindow::setReceipt(){
    QPicture picture;
    QPainter painter;
    QRect rect;
    rect.setX(0);
    rect.setY(0);

    QImage img("pics/pokus.jpg");
    double rate=img.width()/img.height();
    rect.setWidth(200);
    rect.setHeight(200/rate);

    painter.begin(&picture);
    painter.drawImage(rect,img);

    ui->receipt_image->setPicture(picture);
    ui->receiptName->setText(QString::fromUtf8("Boršč"));
    ui->receipt_ingredients->setText(QString::fromUtf8("150g hovädzieho mäsa, 150g bravčového mäsa, 150d údeného mäsa, soľ, ocot, 150g kapusty, 1 cvikla, koreňová zelenina, 150g zemiaky, 150 g rajčiakov alebo rajčiakový pretlak, 2 dl kyslej smotany, mleté čierne korenie"));
    ui->receipt_content->setText(QString::fromUtf8("Do 1,5l studenej vody dáme variť naklepané hovädzie mäso, na menšie kocky pokrájané údené mäso a na rezance pokrájanú cviklu. Neskôr pridáme na kocky pokrájané bravčové mäso, osolíme a uvaríme do mäkka. Potom pridáme na rezance pokrájanú kapustu, zeleninu, rajčiny alebo rajčiakový pretlak, na kocky pokrájané zemiaky a všetko spolu uvaríme do mäkka. Polievku môžeme zahustiť svetlou zápražkou. Nakoniec podľa chuti osolíme, trochu okoreníme a okyslíme. K polievke podávame kyslú smotanu, ktorú pridávame osobitne do každej porcie. Podávame s chlebom."));
}


void MainWindow::setView(QAbstractItemModel *model){
    if(tree!=0){
        disconnect(tree,SIGNAL(dataChanged(QModelIndex,QModelIndex)),this,SLOT(on_data_Changed(QModelIndex,QModelIndex)));
    }
    delete tree;
    tree=model;
    ui->treeView->setModel(tree);
    ui->treeViewEdit->setModel(tree);
    if(currentReceipt){
        int id=currentReceipt->getId();
        delete currentReceipt;
        currentReceipt=new RecipeDescription(db,id);
    }

    displayRecipe();
    connect(tree,SIGNAL(dataChanged(QModelIndex,QModelIndex)),this,SLOT(on_data_Changed(QModelIndex,QModelIndex)));
}

void MainWindow::setReceipt(const QModelIndex &index)
{
    Item* item;
    if(!index.isValid())
        item=root;
    else
        item=static_cast<Item*>(index.internalPointer());

    if(item->isCategory())
        return;

    delete currentReceipt;

    currentReceipt=new RecipeDescription(db,item->getId());

    displayRecipe();
}

void MainWindow::displayInfoMessage(QString &text, QString infotext)
{
    QMessageBox msg;
    msg.setText(text);
    msg.setInformativeText(infotext);
    msg.setIcon(QMessageBox::Information);
    msg.exec();

}

void MainWindow::displayWarning(QString &text, QString infotext)
{
    QMessageBox msg;
    msg.setText(text);
    msg.setInformativeText(infotext);
    msg.setIcon(QMessageBox::Warning);
    msg.exec();
}

bool MainWindow::displayConfirmMessage(QString &text, QString &infotext)
{
    QMessageBox msg;
    msg.setText(text);
    msg.setInformativeText(infotext);
    msg.setStandardButtons(QMessageBox::Yes|QMessageBox::No);
    msg.setIcon(QMessageBox::Question);
    return msg.exec()==QMessageBox::Yes;
}

void MainWindow::init()
{
    /*
      properties set up
      */

    root=new Item(db);
    tree=0;
    currentReceipt=0;
    setView(new CategoryTree(root));

    /*
      Component set up
      */
    //Main display
    ui->display->setCurrentIndex(0);
    //Edit menu
    ui->menuEdit->setEnabled(false);
    //Tree view context menu
    ui->treeViewEdit->addAction(ui->actionAddCategory);
    ui->treeViewEdit->addAction(ui->actionAddSubCategory);
    ui->treeViewEdit->addAction(ui->actionAddReceipt);
    ui->treeViewEdit->addAction(ui->actionRemoveItem);
    ui->treeViewEdit->addAction(ui->actionMoveItem);

    ui->pushButton->setEnabled(false);
    /*
    ui->receipt_image->setFrameStyle(QFrame::Box|QFrame::Raised);
    ui->receipt_image->setLineWidth(1);
    ui->receipt_image->setMidLineWidth(1);

    ui->edit_mode_picture->setFrameStyle(QFrame::Box|QFrame::Raised);
    ui->edit_mode_picture->setLineWidth(1);
    ui->edit_mode_picture->setMidLineWidth(1);
    */
    displayRecipe();


}

/*
  Public slots
  */

void MainWindow::on_actionViewMode_triggered()
{
    ui->display->setCurrentIndex(0);
    ui->menuEdit->setEnabled(false);
    setView(new CategoryTree(root));
}

void MainWindow::on_actionEditMode_triggered()
{
    ui->display->setCurrentIndex(1);
    ui->menuEdit->setEnabled(true);
    setView(new CategoryTreeEditable(root,&db));
    ui->treeViewEdit->setColumnWidth(0,200);
}

void MainWindow::on_actionExit_triggered()
{
    this->close();
}

void MainWindow::on_actionAddCategory_triggered()
{
    QModelIndex index=ui->treeViewEdit->selectionModel()->currentIndex();

    tree->insertRows(index.row()+1,1,index.parent());

}

void MainWindow::on_actionAddSubCategory_triggered()
{
    QModelIndex index=ui->treeViewEdit->selectionModel()->currentIndex();
    /*
    if(index.isValid()){
        Item* item=static_cast<Item*>(index.internalPointer());
        if(!item->isCategory())
            return;
    }
    */
    tree->insertRows(0,1,index);
}

void MainWindow::on_actionRemoveItem_triggered()
{
    QString text;
    QTextStream ss(&text);
    QModelIndex index=ui->treeViewEdit->selectionModel()->currentIndex();
    setReceipt(index);
    //displayRecipe();
     Item* item=static_cast<Item*>(index.internalPointer());
    ss<<QString::fromUtf8("Položka \"")
       <<item->getName()<<QString::fromUtf8("\" bude vymazaná.");
/*
    msg.setText(text);
    msg.setInformativeText(QString::fromUtf8("Naozaj chcete vymazať položku?"));
    msg.setIcon(QMessageBox::Question);
    msg.setStandardButtons(QMessageBox::Yes | QMessageBox::No);
    if(msg.exec()==QMessageBox::No)
        return;
*/
    QString info=QString::fromUtf8("Naozaj chcete vymazať položku?");
    if(!displayConfirmMessage(text,info))
        return;

    bool isCat=item->isCategory();
    if(tree->removeRows(index.row(),1,index.parent())&&!isCat){
        currentReceipt->deleteRecipe();
        qDebug()<<"Receipt deleted";
        delete currentReceipt;
        currentReceipt=0;
        displayRecipe();
    }
    item=0;

}

void MainWindow::on_actionAddReceipt_triggered()
{
    QModelIndex index=ui->treeViewEdit->selectionModel()->currentIndex();
    if(!index.isValid())
        return;
    Item* parentItem=static_cast<Item*>(index.internalPointer());

    if(!parentItem->isCategory())
        return;
    int newItemIndex=parentItem->getNumRows();
    tree->insertRows(newItemIndex,1,index);

    Item* newItem=parentItem->getChild(newItemIndex);
    qDebug()<<newItem->getName();
    if(newItem)
        newItem->addReceipt(db);
    QModelIndex newIndex=tree->index(newItemIndex,0,index);
    ui->treeViewEdit->setCurrentIndex(newIndex);
    qDebug()<<"New Receipt set up";
    setReceipt(newIndex);
}

void MainWindow::on_data_Changed(const QModelIndex &topLeft, const QModelIndex &bottomRight)
{
    setReceipt(topLeft);
}

void MainWindow::displayRecipe(){
    if(currentReceipt==0){
        ui->edit_mode_name->setText(QString::fromUtf8("Vyber recept"));
        ui->scrollArea_2->setEnabled(false);

        ui->receiptName->setText(QString("Vyber recept"));
        ui->receipt_ingredients->setText(QString());
        ui->receipt_content->setText(QString());
        ui->receipt_image->setPicture(QPicture());
        return;
    }

    if(!ui->scrollArea_2->isEnabled())
        ui->scrollArea_2->setEnabled(true);

    ui->edit_mode_name->setText(currentReceipt->getName());
    ui->receiptName->setText(currentReceipt->getName());

    ui->edit_mode_ingredients->setPlainText(currentReceipt->getIngredients());
    ui->receipt_ingredients->setText(currentReceipt->getIngredients());

    ui->edit_mode_content->setPlainText(currentReceipt->getRecipe());
    ui->receipt_content->setText(currentReceipt->getRecipe());

    QPicture picture;
    QPainter painter;
    QRect rect;
    rect.setX(0);
    rect.setY(0);

    const QImage* img=currentReceipt->getImage();
    double rate=img->width()/img->height();
    rect.setWidth(200);
    rect.setHeight(200/rate);

    painter.begin(&picture);
    painter.drawImage(rect,*img);

    ui->receipt_image->setPicture(picture);
    ui->edit_mode_picture->setPicture(picture);

}


/*
void MainWindow::on_treeViewEdit_activated(const QModelIndex &index)
{
    this->setReceipt(index);
}

void MainWindow::on_treeView_activated(const QModelIndex &index)
{
    this->setReceipt(index);
}
*/
void MainWindow::on_edit_mode_ingredients_textChanged()
{
    QString ingredients=ui->edit_mode_ingredients->toPlainText();
    if(currentReceipt)
        currentReceipt->setIngredients(ingredients);
    ui->pushButton->setEnabled(true);
}

void MainWindow::on_edit_mode_content_textChanged()
{
    QString recipe=ui->edit_mode_content->toPlainText();
    if(currentReceipt)
        currentReceipt->setRecipe(recipe);
    ui->pushButton->setEnabled(true);
}

void MainWindow::on_pushButton_clicked()
{
    if(currentReceipt)
        currentReceipt->saveRecipe(db);
    ui->pushButton->setEnabled(false);
    /*
    msg.setText(QString::fromUtf8("Recept uložený."));
    msg.setInformativeText(QString::fromUtf8(""));
    msg.setStandardButtons(0);
    msg.setIcon(QMessageBox::Information);
    msg.exec();
    */
    QString text=QString::fromUtf8("Recept uložený.");
    displayInfoMessage(text);
}

void MainWindow::on_edit_mode_choose_picture_clicked()
{

    QString file=QFileDialog::getOpenFileName(this,tr("Vyber obrazok"),"",
                                 tr("Obrazky (*.png *.jpg *.bmp)"));
    if(file.isNull())
        return;

    currentReceipt->setImage(file);
    displayRecipe();
}

void MainWindow::on_actionSaveReceipt_triggered()
{
    if(currentReceipt)
        currentReceipt->saveRecipe(db);
    ui->pushButton->setEnabled(false);
    /*
    msg.setText(QString::fromUtf8("Recept uložený."));
    msg.setInformativeText(QString::fromUtf8(""));
    msg.setStandardButtons(0);
    msg.setIcon(QMessageBox::Information);
    msg.exec();
    */
    QString text=QString::fromUtf8("Recept uložený.");
    displayInfoMessage(text);
}

void MainWindow::on_actionPrint_triggered()
{
    if(!currentReceipt){
        /*
        msg.setText(QString::fromUtf"Nie je vybraný žiaden recept.")8();
        msg.setInformativeText(QString::fromUtf8(""));
        msg.setStandardButtons(0);
        msg.setIcon(QMessageBox::Warning);
        msg.exec();
        */
        QString text=QString::fromUtf8("Nie je vybraný žiaden recept.");
        displayWarning(text);
        return;
    }
    QPrinter printer;

    QPrintDialog dialog(&printer,this);
    dialog.setWindowTitle(QString::fromUtf8("Vytlač recept."));
    if(dialog.exec()!=QDialog::Accepted)
        return;

    currentReceipt->printRecipe(printer);

}

void MainWindow::on_treeView_clicked(const QModelIndex &index)
{
    this->setReceipt(index);
}

void MainWindow::on_treeViewEdit_clicked(const QModelIndex &index)
{
    this->setReceipt(index);
    this->ui->treeView;
}


void MainWindow::on_actionMoveItem_triggered()
{
    QModelIndex index=ui->treeViewEdit->selectionModel()->currentIndex();
    if(!index.isValid())
        return;
    Item* currentItem=static_cast<Item*>(index.internalPointer());
    QString str=QString::fromUtf8("Presunúť položku");
    MoveDialog::moveItem(currentItem,root,db,str,this);
    //ui->treeViewEdit->setModel(tree);
    ui->treeViewEdit->reset();
    //tree->
}
