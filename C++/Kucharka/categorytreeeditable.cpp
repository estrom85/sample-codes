#include "categorytreeeditable.h"
#include "QDebug"

CategoryTreeEditable::CategoryTreeEditable(Item *root,
                            DBConnection *db, QObject *parent):
    CategoryTree(root,parent){
    this->db=db;
}

QVariant CategoryTreeEditable::data(const QModelIndex &index, int role) const{
    if(role==Qt::EditRole)
        return getItemData(index);

    return CategoryTree::data(index,role);
}

Qt::ItemFlags CategoryTreeEditable::flags(const QModelIndex &index) const{
    if(!index.isValid())
        return 0;

    Qt::ItemFlags flags=Qt::ItemIsSelectable|Qt::ItemIsEnabled|Qt::ItemIsDragEnabled;
    if(index.column()==0)
        flags=flags|Qt::ItemIsEditable;

    Item *item=static_cast<Item*>(index.internalPointer());
    if(!item->isCategory())
        flags=flags|Qt::ItemIsDropEnabled;

    return flags;
}

QVariant CategoryTreeEditable::headerData(int section, Qt::Orientation orientation, int role) const{
    if (!(orientation == Qt::Horizontal && role == Qt::DisplayRole))
        return QVariant();

    if(section==0)
        return QVariant(QString::fromUtf8("Položka"));

    return QVariant(QString("Typ"));
}

int CategoryTreeEditable::columnCount(const QModelIndex &parent) const{
    return 2;
}

bool CategoryTreeEditable::setData(const QModelIndex &index, const QVariant &value, int role){
    //qDebug()<<"pokus";
    if(!index.isValid())
        return false;

    if(role!=Qt::EditRole)
        return false;

    Item* item=static_cast<Item*>(index.internalPointer());
    if(!item)
        return false;
    Item* parent=item->getParent();
    if(!parent)
        return false;
    QString val=value.toString();
    //qDebug()<<item->getRow();
    bool result=parent->editChild(*db,item->getRow(),val);
    if(result)
        emit dataChanged(index,index);

    return result;
}

bool CategoryTreeEditable::insertRows(int row, int count, const QModelIndex &parent){
    Item* parentItem;
    if(!parent.isValid()){
        parentItem=root;
        qDebug()<<"Insert row ("<<(parentItem->getNumRows())<<") -> Invalid index." ;
    }
    else{
        parentItem=static_cast<Item*>(parent.internalPointer());
        qDebug()<<"Insert row ("<<(parentItem->getNumRows())<<") -> Index at ("<<parent.row()<<", "<<parent.column()<<")";
    }

    bool success=false;

    beginInsertRows(parent,parentItem->getNumRows(),parentItem->getNumRows());
    success=parentItem->addChild(*db);
    endInsertRows();

    return success;
}

bool CategoryTreeEditable::removeRows(int row, int count, const QModelIndex &parent){
    Item* parentItem;
    if(!parent.isValid()){
        parentItem=root;
        qDebug()<<"Remove row ("<<row<<") -> Invalid index." ;
    }
    else{
        parentItem=static_cast<Item*>(parent.internalPointer());
        qDebug()<<"Remove row ("<<row<<") -> Index at ("<<parent.row()<<", "<<parent.column()<<")";
    }

    bool success=false;

    beginRemoveRows(parent,row,row);
    success=parentItem->removeChild(*db,row);
    endRemoveRows();

    return success;
}

Qt::DropActions CategoryTreeEditable::supportedDropActions()
{
    return Qt::MoveAction;
}

QVariant CategoryTreeEditable::getItemData(const QModelIndex &index) const
{
    if(!index.isValid())
        return QVariant();

    if(index.column()>2)
        return QVariant();

    if(index.column()==0)
        return CategoryTree::getItemData(index);

    QString cat;

    Item* item=static_cast<Item*>(index.internalPointer());

    if(item->isCategory()) cat=QString::fromUtf8("Kategória");
    else cat=QString::fromUtf8("Recept");

    return QVariant(cat);
}
