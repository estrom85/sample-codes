/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package games_set.card_games32;

/**
 *
 * @author martinm
 */


import java.util.ArrayList;
import java.util.Arrays;

public class CPlayer {
    private String mName;
    int mIndex;
    private ArrayList<CCard> mHand=new ArrayList<CCard>();
    
    /*
     * Constructor
     */
    public CPlayer()
    {
        mHand.ensureCapacity(16);
    }
    
    public CPlayer(String name, int index)
    {
        mName=name;
        mIndex=index;
        mHand.ensureCapacity(16);
    }
    /*
     * Action methods
     */
    
    /**
     * sorts the array
     * 
     * @return 
     * if sorting is successfull returns true, else returns false
     */
    
    private boolean sort(){
        if(mHand.isEmpty()) return false;
        
        CCard[] temp=new CCard[mHand.size()];
        
        mHand.toArray(temp);
        try{
            Arrays.sort(temp);
        }catch (ClassCastException e)
        {
            return false;
        }
        
        
        mHand.clear();
        mHand.addAll(Arrays.asList(temp));
        
        return true;

    }
    
    /**
     * Throws the card out of the hand
     * @param i index of card
     * @return thrown card
     */
    public CCard throwCard(CPacket trash, int i){
        if(i<0||i>mHand.size()-1)
            return null;
        
        
        CCard temp=mHand.get(i);
        
        try{
            trash.putCard(temp);
        }catch(InvalidCardException e)
        {
            return null;
        }
        
        mHand.remove(i);
        return temp;
    }
    
    /**
     * Attempts to draw card from packet
     * 
     * @param packet packet of card to draw from
     * @return true if drawing was successfull
     */
    public boolean draw(CPacket packet){
        CCard temp=packet.takeCard();
        
        if(temp==null)
            return false;
        
        mHand.add(temp);
        sort();
       
        return true;
    }
    
   
    /*
     * Return methods
     */
    
    /**
     * Returns number of cards on hand
     * @return 
     */
    public int getNumberOfCards()
    {
        return mHand.size();
    }
    
    /**
     * Returns i card from hand
     * @param i index of card
     * @return card on hand
     */
    public CCard getCard(int i)
    {
        CCard temp;
        try{
            temp=mHand.get(i);
        }catch (IndexOutOfBoundsException e)
        {
            return null;
        }
        return temp;
    }
    
    /**
     * Returns true if empty
     * @return 
     */
    public boolean isEmpty()
    {
        return mHand.isEmpty();
    }
    
    /**
     * Returns players name
     */
    public String name(){
        return mName;
    }
    
    public int index(){
        return mIndex;
    }
    /**
     * Prints out the hand
     */
    public void print()
    {
        for (int i=0;i<mHand.size();i++)
        {
            System.out.println(mHand.get(i).getTypeString()+" -> "+mHand.get(i).getNumberString());
        }
    }   
}
