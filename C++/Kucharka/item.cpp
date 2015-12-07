#include "item.h"

/*
    Konštruktor triedy

*/
Item::Item(DBConnection &db, Item *parent, int id)
{
    //
    QString sql;
    QTextStream ss(&sql);


    Item* child;



    if(id!=0){
        ss<<"SELECT * FROM Items WHERE id="<<id;
        QueryResult result=db.executeSQL(sql);

        if(!result.next()){
            mId=-1;
            return;
        }


        mId=result.getValueInt("id");
        name=result.getValueString("name");
        mIsCategory=result.getValueBool("isCategory");

        sql.clear();
    }
    else{
        mId=0;
        mIsCategory=true;
    }

    ss<<"SELECT * FROM Items WHERE category="<<id<<" ORDER BY name";
    QueryResult result=db.executeSQL(sql);

    while(result.next()){
        //qDebug()<<"enter loop";
        child=new Item(db,this,result.getValueInt("id"));
        //qDebug()<<"child name:"<<child->getName();
        if(child)
            childList.append(child);
        //qDebug()<<"child added: "<<child;
    }
    //qDebug()<<"loop left";
    this->parent=parent;
}

Item::~Item(){
    qDeleteAll(childList);
   // qDebug()<<"item destructed";
}

QString Item::getName(){
    return name;
}

Item* Item::getChild(int i){
    if(i<0||i>childList.size())
        return 0;

    return childList.value(i,0);
}

int Item::getId(){
    return mId;
}

Item* Item::getParent(){
    return parent;
}

bool Item::isCategory(){
    return mIsCategory;
}

int Item::getNumRows(){
    return childList.size();
}
int Item::getRow(){
    if(!parent)
        return 0;
    return parent->childList.indexOf(this);
}

bool Item::addChild(DBConnection &db){
    if(!this->isCategory()){
        msg.setText(QString::fromUtf8("Táto položka nie je kategória. \nPoložky môžeš pridať len kategóriam!"));
        msg.setInformativeText(QString::fromUtf8(""));
        msg.setIcon(QMessageBox::Warning);
        msg.exec();
        return false;
    }

    int id=db.addItem(QString::fromUtf8("Nová položka"),true,this->getId());
    if(id<1){
        msg.setText(QString::fromUtf8("Nepodarilo sa pridať položku!"));
        msg.setInformativeText(QString::fromUtf8(""));
        msg.setIcon(QMessageBox::Warning);
        msg.exec();
        return false;
    }

    Item* child=new Item(db,this,id);
    if(child)
        this->childList.append(child);

    return true;
}

bool Item::removeChild(DBConnection &db, int i){
    Item* child=this->childList.value(i);
    if(!child)
        return false;

    if(child->getNumRows()>0){
        msg.setText(QString::fromUtf8("Položka nie je prázdna. \nOdstráň najprv všetky podpoložky."));
        msg.setInformativeText(QString::fromUtf8(""));
        msg.setIcon(QMessageBox::Warning);
        msg.exec();
        return false;
    }

    if(!db.removeItem(child->getId()))
        return false;

    this->childList.removeAll(child);

    delete child;

    return true;

}

bool Item::editChild(DBConnection &db, int i, QString &name){
    Item* child=this->childList.value(i);
    if(!child)
        return false;

    db.editItem(child->getId(),name,child->isCategory(),this->getId());

    child->setName(name);

    return true;
}

void Item::setName(QString &name){
    this->name=name;
}

bool Item::addReceipt(DBConnection &db){

    if(this->childList.size()>0)
        return false;

    db.editItem(this->getId(),this->getName(),false,this->parent->getId());
    qDebug()<<"Item changed";
    this->mIsCategory=false;
    QString def=QString("novy recept");
    if(!db.addReceipt(this->getId(),def,def,def))
        return false;
    qDebug()<<"Receipt added";
    return true;
}

bool Item::moveItem(DBConnection &db, Item *newParent)
{
    if(!newParent->isCategory()){
        msg.setText(QString::fromUtf8("Cieľová položka nie je kategória, nemôžeš to presunúť sem."));
        msg.setInformativeText(QString::fromUtf8(""));
        msg.setIcon(QMessageBox::Warning);
        msg.exec();
        return false;
    }
    //zmenit zaznam v databaze
    db.editItem(this->mId,this->name,this->isCategory(),newParent->mId);

    //odstranit referenciu z materskeho objektu
    this->parent->childList.removeAll(this);

    //pridat referenciu do noveho materskeho objektu
    newParent->childList.append(this);

    this->parent=newParent;

    //zobraz informaciu o zmene kategorie
    msg.setText(QString::fromUtf8("Položka bola presunutá."));
    msg.setInformativeText(QString::fromUtf8(""));
    msg.setIcon(QMessageBox::Warning);
    msg.exec();
    return true;
}

QList<Item *> Item::getList()
{
    return childList;
}
